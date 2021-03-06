<?php declare(strict_types = 1);

namespace ForkNetwork\StrictPHPUnit\Standards\ForkNetwork\Tests\PHPUnit;

use ForkNetwork\StrictPHPUnit\StrictTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * Validates trait is added even though the use statement is already imported.
 */
class FooTest extends TestCase
{
    // Contents doesn't matter.
}
