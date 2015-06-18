# Development notes

## Initialize

```sh
$ composer install -o
```

## Production initialize

```sh
$ composer install -o --no-dev
```

## CodeSniffer

```sh
$ vendor/bin/phpcs --standard=style/ruleset.xml  src/ tests/utils/ tests/unit/ tests/integration/
```

## PHPUnit

```sh
$ vendor/bin/phpunit
```

## dBase specification

choose format (pass constant factory)
autodetect format (another factory with chain of resposibility)

http://www.oocities.org/geoff_wass/dBASE/GaryWhite/dBASE/FAQ/qformt.htm
https://github.com/hisamu/php-xbase
http://www.clicketyclick.dk/databases/xbase/format/dbf.html#DBF_STRUCT
http://www.dbase.com/Knowledgebase/INT/db7_file_fmt.htm
http://www.digitalpreservation.gov/formats/fdd/fdd000325.shtml


*Table*
    Traversable -> sequentialy read record
    ArrayAccess -> can access records by index
    Countable -> return recors count

    buffered
        set by size
        set by number

    getRecord(index) : Record
        reads record at specific index

    store(Record)
        adds or updates record

    update(Record)
        updates exisiting record

    add(Record)
        adds new record
        autoincrement should by mpodified

Format
    handle to file
    handle to header

    open(file, mode)
    close
    isValid
    getFileInfo

    getHeader: Header

    readRecord(offset, count = 1) : Record
    storeRecord(Record)

    readHeader: Header
        creates header an reads data from file

    serializeHeader
    deserializeHeader
    serializeRecord
    deserializeRecord

    storeRecords(Records)
        sequences of records stores with one write
        else store one by one
        if new add ad end porocesses autoincrement
        if not new updates at specific index
    readRecords(offset, count) : Record