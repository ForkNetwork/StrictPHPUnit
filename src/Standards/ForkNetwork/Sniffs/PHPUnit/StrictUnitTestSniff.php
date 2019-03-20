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
     * Sniff class whenever it extends from one of the following classes.
     *
     * @var array
     */
    public $extendedClasses = array(
        'TestCase',
    );

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
        // Only sniff classes that extend from certain classes.
        $extendedClass = $phpcsFile->findExtendedClassName($stackPtr);
        if (!\in_array($extendedClass, $this->extendedClasses)) {
            return;
        }

        // Check that the class "ForkNetwork\StrictPHPUnit\StrictTestCase" is used.
        $useStatementPosition = $phpcsFile->findPrevious(T_USE, $stackPtr);
        while ($useStatementPosition !== false) {
            $nextSemicolon = $phpcsFile->findNext(T_SEMICOLON, $useStatementPosition);
            $useStatement = $phpcsFile->getTokensAsString($useStatementPosition, $nextSemicolon - $useStatementPosition);

            if (\strpos($useStatement, 'ForkNetwork\StrictPHPUnit\StrictTestTrait') !== false) {
                break; // Found the use statement!
            }
            $useStatementPosition = $phpcsFile->findPrevious(T_USE, $useStatementPosition - 1);
        }

        // Check that the trait is used.
        $useTraitStatementPosition = $phpcsFile->findNext(T_USE, $stackPtr);
        while ($useTraitStatementPosition !== false) {
            $nextSemicolon = $phpcsFile->findNext(T_SEMICOLON, $useTraitStatementPosition);
            $useStatement = $phpcsFile->getTokensAsString($useTraitStatementPosition, $nextSemicolon - $useTraitStatementPosition);

            if ($useStatement === 'use StrictTestTrait') {
                break; // Found the use statement!
            }

            $useTraitStatementPosition = $phpcsFile->findNext(T_USE, $useTraitStatementPosition + 1);
        }

        // Check that both statements are present.
        if ($useStatementPosition !== false && $useTraitStatementPosition !== false) {
            return;
        }

        $fix = $phpcsFile->addFixableError(self::$message, $stackPtr, self::NOT_STRICT);
        if ($fix) {
            $phpcsFile->fixer->beginChangeset();

            // Ensure the use statement exists.
            if ($useStatementPosition === false) {
                $this->addUseStatement($phpcsFile, $stackPtr);
            }

            // Ensure the trait statement exist.
            if ($useTraitStatementPosition === false) {
                $this->addTraitStatement($phpcsFile, $stackPtr);
            }

            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * Adds the use statement to the bottom of the use statements list. We assume that there is always one use statement
     * because the class needs to extend from TestCase (or another class provided by the settings).
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    private function addUseStatement(File $phpcsFile, int $stackPtr)
    {
        $lastUseStatementLocation = $phpcsFile->findPrevious(T_USE, $stackPtr);
        $endOfLineLocation = $phpcsFile->findEndOfStatement($lastUseStatementLocation);

        $phpcsFile->fixer->addNewline($endOfLineLocation);
        $phpcsFile->fixer->addContent($endOfLineLocation, 'use ForkNetwork\StrictPHPUnit\StrictTestTrait;');
    }

    /**
     * Adds the use trait statement on a new line right after the opening curly braces of the class. We assume one
     * indentation of four spaces. This can be fixed with other sniffs if you have different settings.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    private function addTraitStatement(File $phpcsFile, int $stackPtr)
    {
        $classOpeningPosition = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $stackPtr);

        $phpcsFile->fixer->addNewline($classOpeningPosition);
        $phpcsFile->fixer->addContent($classOpeningPosition, '    use StrictTestTrait;');
    }
}
