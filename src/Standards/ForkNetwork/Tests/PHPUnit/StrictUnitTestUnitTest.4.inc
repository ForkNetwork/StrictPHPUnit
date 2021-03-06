<?php declare(strict_types = 1);

namespace ForkNetwork\StrictPHPUnit\Standards\ForkNetwork\Tests\PHPUnit;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\GlobalState\TestFixture\SnapshotTrait;

/**
 * Validates use statement is added even though the trait is already included.
 */
class FooTest extends TestCase
{
    use StrictTestTrait;
    use SnapshotTrait;
}
