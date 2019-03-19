<?php declare(strict_types = 1);

namespace ForkNetwork\StrictPHPUnit;

use PHPUnit\Framework\MockObject\MockBuilder;

trait StrictTestTrait
{
    /**
     * Returns a builder object to create mock objects using a fluent interface.
     * To ensure strictness, set the auto return value generation to false by default. (can be overwritten by calling enableAutoReturnValueGeneration)
     *
     * @param string|string[] $className
     * @return MockBuilder
     */
    public function getMockBuilder($className): MockBuilder
    {
        return parent::getMockBuilder($className)
            ->disableAutoReturnValueGeneration(); // Ensure no unexpected methods are called.
    }
}
