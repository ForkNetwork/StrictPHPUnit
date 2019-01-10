<?php declare(strict_types = 1);

namespace ForkNetwork\StrictPHPUnit\Standards\ForkNetwork\Tests\PHPUnit;

use ForkNetwork\StrictPHPUnit\Standards\ForkNetwork\Tests\AbstractSniffTest;

class StrictUnitTestUnitTest extends AbstractSniffTest
{
    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile Optional argument that returns the file name of the current test.
     * @return int[]|array
     */
    protected function getErrors(string $testFile = 'StrictUnitTestUnitTest.1.inc'): array
    {
        return array(
            11 => 1,
        );
    }

    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @param string $testFile Optional argument that returns the file name of the current test.
     * @return int[]|array
     */
    protected function getWarnings(string $testFile = 'StrictUnitTestUnitTest.1.inc'): array
    {
        return array();
    }
}
