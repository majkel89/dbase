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
   4. [Record object](#record-object)
       1. [Reading data from record](#reading-data-from-record)
       2. [Writing data to record](#writing-data-to-record)
   4. [Record object](#record-object)
   5. [Updating tables](#updating-tables)
   6. [Deleting records](#deleting-records)
   7. [Transactions](#transactions)
   8. [Defining tables](#defining-tables)
       1. [Creating table from another table](#creating-table-from-another-table)
   9. [Filters](#filters)
       1. [Using filters](#using-filters)
       2. [Writing custom filter](#writing-custom-filter)

## Supported formats

 - dBASE III
 - dBASE III PLUS
 
##### Supported memo formats

 - DBT
 - FPT

## Installation

### Composer

Using composer to install this library is strongly recommended.

````
composer require org.majkel/dbase
````

Then in your script use this line of code

````php
require_once 'vendor/autoload.php'
````

### Old-fashion style

Download library and place it somewhere on disk.

Then in your script use this line of code

````php
require_once 'DBASE/LIB/DIR/autoloader.php';
````


## Documentation

### Reading tables

Table object is both array accessible and traversable.
You can loop over it as collection or read specific record by it's index.

````php
require_once 'vendor/autoload.php'

use org\majkel\dbase\Table;

$totalSum = 0;

$dbf = Table::fromFile('some/table.dbf');

foreach ($dbf as $record) {
    // returns all records includeing deleted ones
    if (!$record->isDeleted()) {
        $totalSum += $record->int_val;
    }
}

echo "Total sum is $totalSum, 5th description: {$record[4]['description']}\n";
````

### Inserting rows

You can insert records as record object or as an associative array.

 > Note that insert operation is not atomic. Use transactions to achieve integrity
   safety.

````
require_once 'vendor/autoload.php'

use org\majkel\dbase\Table;
use org\majkel\dbase\Record;

$dbf = Table::fromFile('some/table.dbf');

$record = new Record();
$record->fieldBool = true;
$record->fieldInt  = 123;
$record->fieldChar = 'some text 1';
$record->fieldMemo = 'some long text';

$dbf->insert($record);

$dbf->insert([
    'fieldBool' => false,
    'fieldInt'  => 321,
    'fieldChar' => 'some text 2',
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

### Record object

Record is basically ArrayObject. Object that can be treated as array.

#### Reading data from record

````php
require_once 'vendor/autoload.php'

use org\majkel\dbase\Table;

$dbf = Table::fromFile('some/table.dbf');

// fetch first record
$record = $dbf[0];

echo "int  field: {$record->number}\n"; // returns integer
echo "bool field: {$record->boolean}\n"; // returns boolean
echo "date field: {$record->date->format('Y-m-d')}\n"; // return DateTime object
echo "text field: {$record->text}\n"; // returns string
echo "memo field: {$record->memo}\n"; // returns string (not entity id)
echo "memo field id: {$record->getMemoEntryId('memo')}\n"; // returns entity id for memo field `memo`
echo "is record deleted: {$record->isDeleted('memo')}\n"; // returns whether record is deleted

// ... or ...

echo "int  field: {$record['number']}\n"; // returns integer
echo "bool field: {$record['boolean']}\n"; // returns boolean
echo "date field: {$record['date']->format('Y-m-d')}\n"; // return DateTime object
echo "text field: {$record['text']}\n"; // returns string
echo "memo field: {$record['memo']}\n"; // returns string (not entity id)
echo "memo field id: {$record->getMemoEntryId('memo')}\n"; // returns entity id for memo field `memo`
echo "is record deleted: {$record->isDeleted('memo')}\n"; // returns whether record is deleted

// you can loop over fields in the record
foreach ($record as $fieldName => $fieldValue) {
    echo "$fieldName = $fieldValue\n";
}
````

#### Writing data to record

````php
require_once 'vendor/autoload.php'

use org\majkel\dbase\Table;

$dbf = Table::fromFile('some/table.dbf');

// fetch first record
$record = $dbf[0];

$record->number  = 123;
$record->boolean = true;
$record->date    = new DateTime();
$record->text    = 'some text';
$record->memo    = 'some longer text';

// ... or ...

$record['number']  = 123;
$record['boolean'] = true;
$record['date']    = new DateTime();
$record['text']    = 'some text';
$record['memo']    = 'some longer text';
````

## Updating tables

 > Note that update operation is not atomic. Use transactions to achieve integrity
   safety.

````php
require_once 'vendor/autoload.php'

use org\majkel\dbase\Table;

$dbf = Table::fromFile('some/table.dbf');

foreach ($dbf as $record) {
    $record->int_val += 10;
    $dbf->update($record); // header is updated everytime
}
````

## Deleting records

> Do not use `Record::setDeleted` to delete records

````php
require_once 'vendor/autoload.php'

use org\majkel\dbase\Table;

$dbf = Table::fromFile('some/table.dbf');

// delete 7th record
$dbf->delete(6);

// undelete 6th record
$dbf->markDelete(5, false);
````

### Transactions

Transactions can prevent two processes from updating the same file.

When some process cannot acquire lock on the table exception is being thrown.

Transactions can also save you from unnecessary header updates. Header is updated at the end
of transaction.

````php
require_once 'vendor/autoload.php'

use org\majkel\dbase\Table;

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
require_once 'vendor/autoload.php'

use org\majkel\dbase\Builder;
use org\majkel\dbase\Format;
use org\majkel\dbase\Field;

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
require_once 'vendor/autoload.php'

use org\majkel\dbase\Builder;
use org\majkel\dbase\Format;
use org\majkel\dbase\Field;

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
require_once 'vendor/autoload.php'

use org\majkel\dbase\Builder;
use org\majkel\dbase\filter\TrimFilter;
use your\CurrencyFilter;

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
require_once 'vendor/autoload.php'

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
