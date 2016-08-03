dbase
=====

[![Build Status](https://travis-ci.org/majkel89/dbase.svg?branch=master)](https://travis-ci.org/majkel89/dbase)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1bbc08b6-12a4-4aa5-9908-abb2684e2c45/mini.png)](https://insight.sensiolabs.com/projects/1bbc08b6-12a4-4aa5-9908-abb2684e2c45)
[![Latest Stable Version](https://poser.pugx.org/org.majkel/dbase/v/stable)](https://packagist.org/packages/org.majkel/dbase)
[![Total Downloads](https://poser.pugx.org/org.majkel/dbase/downloads)](https://packagist.org/packages/org.majkel/dbase)
[![Latest Unstable Version](https://poser.pugx.org/org.majkel/dbase/v/unstable)](https://packagist.org/packages/org.majkel/dbase)
![PHP Version](https://img.shields.io/badge/version-PHP%205.3%2B-lightgrey.svg)
[![License](https://poser.pugx.org/org.majkel/dbase/license)](https://packagist.org/packages/org.majkel/dbase)

Library for processing dbase tables.

## Table of Contents

1. [Supported formats](#supported-formats)
    1. [Supported memo formats](#supported-memo-formats)
2. [Installation](#installation)
3. [Documentation](#documentation)
   1. [Reading tables](#reading-tables)
   2. [Inserting rows](#inserting-rows)
   3. [Automatic type conversion](#automatic-type-conversion)
   4. [Updating tables](#updating-tables)
   5. [Transactions](#transactions)
   5. [Defining tables](#defining-tables)
       1. [Creating table from another table](#creating-table-from-another-table)
   6. [Filters](#filters)
       1. [Using filters](#using-filters)
       2. [Writing custom filter](#writing-custom-filter)

## Supported formats

 - dBASE III
 - dBASE III PLUS
 
##### Supported memo formats

 - DBT
 - FPT

## Installation

Using composer to install this library is strongly recommended.

````
composer require org.majkel/dbase
````

## Documentation

### Reading tables

Table object is both array accessible and traversable.
You can loop over it as collection or read specific record by it's index.

````php
$totalSum = 0;

$dbf = Table::fromFile('some/table.dbf');

foreach ($dbf as $record) {
    $totalSum += $record->int_val;
}

echo "Total sum is $totalSum, 5th description: {$record[4]['description']}\n";
````

### Inserting rows

You can insert records as record object or as an associative array.

 > Note that insert operation is not atomic. Use transactions to achieve integrity
   safety.

````
$dbf = Table::fromFile('some/table.dbf');

$record = new Record();
$record->fiedBool = true;
$record->fiedInt = 123;
$record->fiedChar = 'some text 1';

$dbf->insert($record);

$dbf->insert([
    'fiedBool' => false,
    'fiedInt' => 321,
    'fiedChar' => 'some text 2',
]);
````

### Automatic type conversion

Dbase and PHP types are automatically converted during fetching and storing of rows.

Dbase type | Type name | Possible values | PHP type
-----------|-----------|-----------------|----------
C          | Character | _any string_    | string
D          | Date      | DDMMYY          | DateTime
L          | Logical   | [YTNF?]         | boolean
M          | Memo      | _any string_    | string
N          | Numeric   | [0-9]           | int

## Updating tables

 > Note that update operation is not atomic. Use transactions to achieve integrity
   safety.

````php
$dbf = Table::fromFile('some/table.dbf');

foreach ($dbf as $record) {
    $record->int_val += 10;
    $dbf->update($record); // header is updated everytime
}
````

### Transactions

Transactions can prevent two processes from updating the same file.

When some process cannot acquire lock on the table exception is being thrown.

Transactions can also save you from unnecessary header updates. Header is updated at the end
of transaction.

````php
$dbf = Table::fromFile('some/table.dbf');

// header is updated. Transaction flag is set
$dbf->beginTransaction();

foreach ($dbf as $record) {
    $record->int_val += 10;
    $dbf->update($record);  // header is not written
}

// duplicate last row
$dbf->insert($record); // header is not written

// header is written, transaction flag is cleared, recond count is updated
$dbf->endTransaction();
````

### Defining tables

To construct new table use builder object.

````php
$table = Builder::create()
    ->setFormatType(Format::DBASE3)
    ->addField(Field::create(Field::TYPE_CHARACTER)->setName('str')->setLength(15))
    ->addField(Field::create(Field::TYPE_LOGICAL)->setName('bool'))
    ->addField(Field::create(Field::TYPE_NUMERIC)->setName('num'))
    ->build('destination.dbf');

for ($i = 1; $i <= 3; ++$i) {
    $table->insert([
        'str' => "Str $i",
        'bool' => false,
        'num' => $i,
    ]);
}
````

#### Creating table from another table

You can create new table form existing table definition.

````
$table = Builder::fromFile('source.dbf')
    ->setFormatType(Format::DBASE3)
    ->addField(Field::create(Field::TYPE_NUMERIC)->setName('newField1'))
    ->build('destination.dbf');

for ($i = 1; $i <= 3; ++$i) {
    $table->insert([
        'oldField1' => "Str $i",
        'oldField2' => false,
        'newField1' => $i,
    ]);
}
````

### Filters

Although values are automatically converted based on column type sometimes it is
necessary to perform additional processing.
To achieve that you can add filters on columns. 

#### Using filters

````php
$dbf = Table::fromFile('some/table.dbf');
$dbf->getHeader()->getField('price')
    ->addFilter(new TrimFilter())
    ->addFilter(new CurrencyFilter(',', '.'));

foreach ($dbf as $record) {
    // ...
}
````

Filters are applied during loading in the order they are defined.
During serialization filters are applied in reversed order.

#### Writing custom filter

````php
use org\majkel\dbase\FilterInterface;
use org\majkel\dbase\Field;

class CurrencyFilter extends FilterInterface
{
    /** @var string */
    private $inputDot;
    /** @var string */
    private $outputDot;

    /**
     * @param string $inputDot
     * @param string $outputDot
     */
    public function __construct($inputDot, $outputDot)
    {
        $this->inputDot = $inputDot;
        $this->outputDot = $outputDot;
    }

    /**
     * From table value to PHP value
     *
     * @param mixed $value
     * @return mixed
     */
    public function toValue($value)
    {
        return str_replace($this->inputDot, $this->outputDot, $value);
    }

    /**
     * From PHP value to table value
     *
     * @param mixed $value
     * @return mixed
     */
    public function fromValue($value)
    {
        return str_replace($this->outputDot, $this->inputDot, $value);
    }

    /**
     * Filter can be applied on string like columns
     *
     * @param integer $type
     * @return boolean
     */
    public function supportsType($type)
    {
        return in_aray($type, [Field::TYPE_CHARACTER, Field::TYPE_MEMO]);
    }
}
````
