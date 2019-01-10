<?php declare(strict_types = 1);

namespace ForkNetwork\StrictPHPUnit\Standards\ForkNetwork\Sniffs\PHPUnit;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class StrictUnitTestSniff implements Sniff
{
    const NOT_STRICT = 'NotStrict';

    private static $message = 'Unit test does not adhere to strict standards.';

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('PHP');

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array
     */
    public function register(): array
    {
        return array(T_CLASS);
    }

    /**
     * Called when one of the token types that this sniff is listening for
     * is found.
     *
     * The stackPtr variable indicates where in the stack the token was found.
     * A sniff can acquire information this token, along with all the other
     * tokens within the stack by first acquiring the token stack:
     *
     * <code>
     *    $tokens = $phpcsFile->getTokens();
     *    echo 'Encountered a '.$tokens[$stackPtr]['type'].' token';
     *    echo 'token information: ';
     *    print_r($tokens[$stackPtr]);
     * </code>
     *
     * If the sniff discovers an anomaly in the code, they can raise an error
     * by calling addError() on the \PHP_CodeSniffer\Files\File object, specifying an error
     * message and the position of the offending token:
     *
     * <code>
     *    $phpcsFile->addError('Encountered an error', $stackPtr);
     * </code>
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     * @param int $stackPtr The position in the PHP_CodeSniffer
     *                                               file's token stack where the token
     *                                               was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Check that the current class extends "TestCase".
        $extendedClass = $phpcsFile->findExtendedClassName($stackPtr);
        if ($extendedClass !== 'TestCase') {
            return;
        }

        // Check that the class "PHPUnit\Framework\TestCase" is used.
        $useStatementPosition = $phpcsFile->findPrevious(T_USE, $stackPtr);
        while ($useStatementPosition !== false) {
            $nextSemicolon = $phpcsFile->findNext(T_SEMICOLON, $useStatementPosition);
            $useStatement = $phpcsFile->getTokensAsString($useStatementPosition, $nextSemicolon - $useStatementPosition);

            if (\strpos($useStatement, 'PHPUnit\Framework\TestCase') !== false) {
                break; // Found the use statement!
            }
            $useStatementPosition = $phpcsFile->findPrevious(T_USE, $useStatementPosition - 1);
        }

        // No use statement for the class can be found.
        if ($useStatementPosition === false) {
            return;
        }

        $fix = $phpcsFile->addFixableError(self::$message, $stackPtr, self::NOT_STRICT);
        if ($fix) {
            $phpcsFile->fixer->beginChangeset();

            // Fix the use statement.
            $phpcsFile->fixer->replaceToken($useStatementPosition + 2, 'ForkNetwork\StrictPHPUnit');
            $phpcsFile->fixer->replaceToken($useStatementPosition + 6, 'StrictTestCase');

            // Fix the extends class reference.
            $phpcsFile->fixer->replaceToken($stackPtr + 6, 'StrictTestCase');

            $phpcsFile->fixer->endChangeset();
        }
    }
}
