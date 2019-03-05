# PHPUnit Finder


This is a helper CLI tool that queries phpunit.xml files to get a list of test
filenames and print them. This is useful if you want to split tests to run them
in parallel based on timings on CI tools such as CirclCI.

## Installation

Install with composer:

`composer require --dev previousnext/phpunit-finder`

## Usage

You can run with defaults using:

`./vendor/bin/phpunit-finder`

By default, it will look for all test suites to scan.

You can filter by specific test suites as follows:

`./vendor/bin/phpunit-finder unit kernel`

## Configuration

phpunit-finder assumes you have a phpunit.xml in the root of your project. You can
override the path using the `--config-file` option.

It also assumes your tests bootstrap file is found in `tests/bootstrap.php`.
You can override this with the `--bootstrap-file` option.
