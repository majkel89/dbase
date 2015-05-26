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
