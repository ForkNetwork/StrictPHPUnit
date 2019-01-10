<?php declare(strict_types = 1);

namespace ForkNetwork\StrictPHPUnit\Tests\Framework\Fixtures;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GenericClass
{
    /**
     * Because the StrictTestCase is already dependent on the TestCase class of PHPUnit we can use that class for
     * expected method calls instead of creating multiple fixtures.
     *
     * @param TestCase|MockObject $testCase
     * @return bool
     */
    public function callsOneMethod(TestCase $testCase): bool
    {
        return $testCase->hasExpectationOnOutput();
    }
}
