<?php declare(strict_types = 1);

namespace ForkNetwork\StrictPHPUnit\Standards\ForkNetwork\Tests;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use PHP_CodeSniffer\Util\Common;

/**
 * This is a wrapper for the AbstractSniffUnitTest that the PHP CodeSniffer library offers. Their set up requires
 * multiple test suites to be set up. By overwriting some variables in the setUp method we can run each test
 * individually without having a complex test suite.
 */
abstract class AbstractSniffTest extends AbstractSniffUnitTest
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
    abstract protected function getErrors(string $testFile = 'UnitTest.inc'): array;

    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @param string $testFile Optional argument that returns the file name of the current test.
     * @return int[]|array
     */
    abstract protected function getWarnings(string $testFile = 'UnitTest.inc'): array;

    /**
     * Sets the directories where the standards and tests can be found. The sniff codes array also needs to be set for
     * the errors to work properly.
     *
     * @throws RuntimeException
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $this->setUpCodesnifferVariables();
        $this->setUpCustomRuleset();

        parent::setUp();
    }

    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile Optional argument that returns the file name of the current test.
     * @return int[]|array
     */
    final protected function getErrorList(string $testFile = 'UnitTest.inc'): array
    {
        // Have at least one assertion that succeeds.
        $this->assertTrue(true);

        return $this->getErrors($testFile);
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
    final protected function getWarningList($testFile = 'UnitTest.inc'): array
    {
        // Have at least one assertion that succeeds.
        $this->assertTrue(true);

        return $this->getWarnings($testFile);
    }

    /**
     * Set up the code sniffer variables.
     * Unfortunately, there is no way around using globals.
     *
     * @throws \ReflectionException
     */
    private function setUpCodesnifferVariables(): void
    {
        $class = \get_class($this);
        $standardsDirectory = $this->resolveStandardsDirectory($class);

        $GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'][$class] = $standardsDirectory;
        $GLOBALS['PHP_CODESNIFFER_TEST_DIRS'][$class] = $standardsDirectory . '/Tests/';
        $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES'] = [];
        $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'] = [];

//        if (defined('PHP_CODESNIFFER_VERBOSITY') === false) {
//            define('PHP_CODESNIFFER_VERBOSITY', 0);
//        }
    }

    /**
     * Set up a custom ruleset for the test.
     *
     * @throws RuntimeException
     */
    private function setUpCustomRuleset(): void
    {
        $sniffCode = Common::getSniffCode(\get_class($this));
        list($standardName, $categoryName, $sniffName) = \explode('.', $sniffCode);

        $rulesetPath = $this->getCustomRulesetPath($categoryName, $sniffName);
        if ($rulesetPath === null) {
            return;
        }

        $config = new Config();
        $config->cache = false;

        $ruleset = new Ruleset($config);
        $ruleset->processRuleset($rulesetPath);

        $GLOBALS['PHP_CODESNIFFER_CONFIG'] = $config;
        $GLOBALS['PHP_CODESNIFFER_RULESETS'][$standardName] = $ruleset;
    }

    /**
     * Returns the location of a custom ruleset. This is sometimes necessary when testing rules where you need to set
     * a property.
     *
     * @param string $categoryName
     * @param string $sniffName
     * @return string|null Returns null when no custom ruleset should be used.
     */
    private function getCustomRulesetPath(string $categoryName, string $sniffName): ?string
    {
        $rulesetPath = \sprintf('%s/%s/%sRuleset.xml', __DIR__, $categoryName, $sniffName);
        if (!\file_exists($rulesetPath)) {
            return null;
        }

        return $rulesetPath;
    }

    /**
     * Resolve the directory where the standards are based on the class name.
     * Unfortunately this has to be done using reflection. There is no way of knowing if this file is called locally
     * or as a dependency.
     *
     * @param string $class
     * @return string
     * @throws \ReflectionException
     */
    private function resolveStandardsDirectory(string $class): string
    {
        $reflector = new \ReflectionClass($class);
        $fileLocation = \dirname($reflector->getFileName());
        $standardsDirectory = \realpath($fileLocation . '/../../');

        return $standardsDirectory;
    }
}
