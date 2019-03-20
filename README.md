# StrictPHPUnit

[![Latest version](https://img.shields.io/packagist/v/forknetwork/strict-phpunit.svg?style=flat-square&colorB=007EC6)](https://packagist.org/packages/forknetwork/strict-phpunit)
[![Downloads](https://img.shields.io/packagist/dt/forknetwork/strict-phpunit.svg?style=flat-square&colorB=007EC6)](https://packagist.org/packages/forknetwork/strict-phpunit)
[![Travis build status](https://img.shields.io/travis/ForkNetwork/StrictPHPUnit/master.svg?label=travis&style=flat-square)](https://travis-ci.org/ForkNetwork/StrictPHPUnit)

A simple PHPUnit extension that disallows unexpected method calls.

For PHPUnit 7.1 and up.

## Installation
```bash
composer require forknetwork/strict-phpunit ^2.0
```

## How to use
The `StrictTestTrait` can be used by any class that extends from `TestCase` of PHPUnit. This trait can be added automatically using the PHP Code Sniffer.

**Before**
```php
use PHPUnit\Framework\TestCase;

class YourTest extends TestCase
{
    // ...
}
```

**After**
```php
use ForkNetwork\StrictPHPUnit\StrictTestTrait;
use PHPUnit\Framework\TestCase;

class YourTest extends TestCase
{
    use StrictTestTrait;
    // ...
}
```

### Overwrite strict mock generation
By default all mocks will be strict, but in some cases that won't work. For example, PHPUnit doesn't handle the destruct method for mocks well. In those cases you might want to enable the value generation again.

**Before**
```php
private function createFooMock()
{
    return $this->createMock('\Bar\Foo');
}
```

**After**
```php
private function createFooMock()
{
    return $this->getMockBuilder('\Bar\Foo')
        ->enableAutoReturnValueGeneration();
        ->getMock();
}
```

Another option would to remove the trait from the class and manually add `->disableAutoReturnValueGeneration()` onto the mocks your want strict.

## Optional: PHP code sniffer and fixer
This library comes with a code sniff with auto-fixer. Just include the reference in your `phpcs.xml(.dist)` file and run the code sniffer as usual.
```xml
<rule ref="./vendor/forknetwork/strict-phpunit/src/Standards/ForkNetwork/ruleset.xml"/>
```

By default it will sniff all classes that extend from `TestCase` (PHPUnit). You can change which extended class it should sniff by adding this to your configuration.
```xml
<rule ref="ForkNetwork.PHPUnit.StrictUnitTest">
    <properties>
        <property name="extendedClasses" type="array" value="TestCase,ExtendedTestCase,AnotherCustomTestCase"/>
    </properties>
</rule>
```

Please note that this sniff only ensures the trait and use statements are added. It does not sort or pay attention to the rest of your code style.

**Known limitations**  
The current implementation of the sniff doesn't support the following situations:
 * Comma separated use trait statements (e.g. `use StrictUnitTrait, FooTrait;`)
 * FQCN or partial use trait statements (e.g. `use Traits\StrictUnitTest;`)
