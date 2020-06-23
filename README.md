# Schemarkdown

[![Build Status](https://travis-ci.com/MilesChou/schemarkdown.svg?branch=master)](https://travis-ci.com/MilesChou/schemarkdown)
[![codecov](https://codecov.io/gh/MilesChou/schemarkdown/branch/master/graph/badge.svg)](https://codecov.io/gh/MilesChou/schemarkdown)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/67591518c2cd4c12bb73004998d08e29)](https://www.codacy.com/manual/MilesChou/schemarkdown)
[![Latest Stable Version](https://poser.pugx.org/MilesChou/schemarkdown/v/stable)](https://packagist.org/packages/MilesChou/schemarkdown)
[![Total Downloads](https://poser.pugx.org/MilesChou/schemarkdown/d/total.svg)](https://packagist.org/packages/MilesChou/schemarkdown)
[![License](https://poser.pugx.org/MilesChou/schemarkdown/license)](https://packagist.org/packages/MilesChou/schemarkdown)

The core library for generate Markdown document from database schema.

## Installation

Use Composer to install:

```bash
composer require mileschou/schemarkdown
```

## Usage

Use following command to generate schema documents:

```bash
php artisan schemarkdown
```

Schema document will store in `generated` directory default. Use the `--output-dir` option to change.

In the other framework, you must provide config file like Laravel. Use `--config-file` option to specify customize config file.

This tool will load `.env` before load config. Use the `--env` option to specify .env path. 

Use the `--connection` option to specify **connection name** in Laravel config to generate documents of one database.

## Example

Here is example [SQL](/examples/examples.sql), import MySQL and run following command:

```
php artisan schemarkdown --config-file=tests/Fixtures/database.php --connection=examples --output-dir=examples
```

It will generate this [Markdown documents](/examples).

## Troubleshooting

Use `-vv` option to see info log.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
