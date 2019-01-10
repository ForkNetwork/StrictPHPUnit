<?php declare(strict_types = 1);

namespace ForkNetwork\StrictPHPUnit\Standards\ForkNetwork\Tests\PHPUnit;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;

/**
 * Validates only the extends class is fixed and the implemented classes are not altered.
 */
class FooTest extends TestCase implements Test
{
    // Contents doesn't matter.
}
