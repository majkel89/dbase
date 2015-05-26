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
$ vendor/bin/phpcs --standard=style/ruleset.xml  src/ tests/src/ tests/utils/
```

## PHPUnit

```sh
$ vendor/bin/phpunit
```

## dBase specification

http://www.oocities.org/geoff_wass/dBASE/GaryWhite/dBASE/FAQ/qformt.htm
https://github.com/hisamu/php-xbase
http://www.clicketyclick.dk/databases/xbase/format/dbf.html#DBF_STRUCT
http://www.dbase.com/Knowledgebase/INT/db7_file_fmt.htm
http://www.digitalpreservation.gov/formats/fdd/fdd000325.shtml
