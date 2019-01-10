# StrictPHPUnit

[![Latest version](https://img.shields.io/packagist/v/forknetwork/strict-phpunit.svg?style=flat-square&colorB=007EC6)](https://packagist.org/packages/forknetwork/strict-phpunit)
[![Downloads](https://img.shields.io/packagist/dt/forknetwork/strict-phpunit.svg?style=flat-square&colorB=007EC6)](https://packagist.org/packages/forknetwork/strict-phpunit)
[![Travis build status](https://img.shields.io/travis/ForkNetwork/StrictPHPUnit/master.svg?label=travis&style=flat-square)](https://travis-ci.org/ForkNetwork/StrictPHPUnit)

A simple PHPUnit extension that disallows unexpected method calls.

For PHPUnit 7.1 and up.

## Installation
```bash
composer require forknetwork/strict-phpunit ^1.0
```

## How to use
The `StrictTestCase` is a drop-in replacement for the `TestCase` of PHPUnit. You can replace it automatically using the PHP code sniffer/fixer or update your code manually:

**Before**
```php
use PHPUnit\Framework\TestCase;

class YourTest extends TestCase
```

**After**
```php
use ForkNetwork\StrictPHPUnit\Framework\StrictTestCase;

class YourTest extends StrictTestCase
```

## Optional: PHP code sniffer and fixer
This library comes with a code sniff with auto-fixer. Just include the reference in your `phpcs.xml(.dist)` file and run the code sniffer as usual.
```xml
<rule ref="./vendor/forknetwork/strict-phpunit/src/Standards/ForkNetwork/ruleset.xml"/>
```

For sorting your use statements alphabetically afterwards, please take a look at [SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses](https://github.com/slevomat/coding-standard/blob/master/README.md#slevomatcodingstandardnamespacesalphabeticallysorteduses-).
