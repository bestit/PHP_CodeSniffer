<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer;

use PHP_CodeSniffer\Files\File;
use function array_walk;
use function func_get_args;
use function get_object_vars;

/**
 * Copy of the file api to provide an file decorator.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer
 */
abstract class AbstractFileDecorator extends File
{
    /**
     * The CodeSniffer file
     *
     * @var File
     */
    private $baseFile;

    /**
     * File constructor.
     *
     * @param File $baseFile CodeSniffer file
     */
    public function __construct(File $baseFile)
    {
        $this->takeProperties($baseFile);

        $this->baseFile = $baseFile;
    }

    /**
     * Returns the wrapped classes method result.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args = [])
    {
        return $this->getBaseFile()->{$method}(...$args);
    }
    
    /**
     * Records an error against a specific token in the file.
     *
     * @param string $error The error message.
     * @param int $stackPtr The stack position where the error occurred.
     * @param string $code A violation code unique to the sniff message.
     * @param array $data Replacements for the error message.
     * @param int $severity The severity level for this error. A value of 0
     *                          will be converted into the default severity level.
     * @param boolean $fixable Can the error be fixed by the sniff?
     *
     * @return boolean
     */
    public function addError(
        $error,
        $stackPtr,
        $code,
        $data = [],
        $severity = 0,
        $fixable = false
    ) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Records an error against a specific line in the file.
     *
     * @param string $error The error message.
     * @param int $line The line on which the error occurred.
     * @param string $code A violation code unique to the sniff message.
     * @param array $data Replacements for the error message.
     * @param int $severity The severity level for this error. A value of 0
     *                         will be converted into the default severity level.
     *
     * @return boolean
     */
    public function addErrorOnLine(
        $error,
        $line,
        $code,
        $data = [],
        $severity = 0
    ) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Records a fixable error against a specific token in the file.
     *
     * Returns true if the error was recorded and should be fixed.
     *
     * @param string $error The error message.
     * @param int $stackPtr The stack position where the error occurred.
     * @param string $code A violation code unique to the sniff message.
     * @param array $data Replacements for the error message.
     * @param int $severity The severity level for this error. A value of 0
     *                         will be converted into the default severity level.
     *
     * @return boolean
     */
    public function addFixableError(
        $error,
        $stackPtr,
        $code,
        $data = [],
        $severity = 0
    ) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Records a fixable warning against a specific token in the file.
     *
     * Returns true if the warning was recorded and should be fixed.
     *
     * @param string $warning The error message.
     * @param int $stackPtr The stack position where the error occurred.
     * @param string $code A violation code unique to the sniff message.
     * @param array $data Replacements for the warning message.
     * @param int $severity The severity level for this warning. A value of 0
     *                         will be converted into the default severity level.
     *
     * @return boolean
     */
    public function addFixableWarning(
        $warning,
        $stackPtr,
        $code,
        $data = [],
        $severity = 0
    ) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Adds an error to the error stack.
     *
     * @param boolean $error Is this an error message?
     * @param string $message The text of the message.
     * @param int $line The line on which the message occurred.
     * @param int $column The column at which the message occurred.
     * @param string $code A violation code unique to the sniff message.
     * @param array $data Replacements for the message.
     * @param int $severity The severity level for this message. A value of 0
     *                          will be converted into the default severity level.
     * @param boolean $fixable Can the problem be fixed by the sniff?
     *
     * @return boolean
     */
    protected function addMessage($error, $message, $line, $column, $code, $data, $severity, $fixable)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Records a warning against a specific token in the file.
     *
     * @param string $warning The error message.
     * @param int $stackPtr The stack position where the error occurred.
     * @param string $code A violation code unique to the sniff message.
     * @param array $data Replacements for the warning message.
     * @param int $severity The severity level for this warning. A value of 0
     *                          will be converted into the default severity level.
     * @param boolean $fixable Can the warning be fixed by the sniff?
     *
     * @return boolean
     */
    public function addWarning(
        $warning,
        $stackPtr,
        $code,
        $data = [],
        $severity = 0,
        $fixable = false
    ) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Records a warning against a specific token in the file.
     *
     * @param string $warning The error message.
     * @param int $line The line on which the warning occurred.
     * @param string $code A violation code unique to the sniff message.
     * @param array $data Replacements for the warning message.
     * @param int $severity The severity level for this warning. A value of 0 will
     *                         will be converted into the default severity level.
     *
     * @return boolean
     */
    public function addWarningOnLine(
        $warning,
        $line,
        $code,
        $data = [],
        $severity = 0
    ) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Remove vars stored in this file that are no longer required.
     *
     * @return void
     */
    public function cleanUp()
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Disables caching of this file.
     *
     * @return void
     */
    public function disableCaching()
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the position of the last non-whitespace token in a statement.
     *
     * @param int $start The position to start searching from in the token stack.
     * @param int|array $ignore Token types that should not be considered stop points.
     *
     * @return int
     */
    public function findEndOfStatement($start, $ignore = null)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the name of the class that the specified class extends.
     * (works for classes, anonymous classes and interfaces)
     *
     * Returns FALSE on error or if there is no extended class name.
     *
     * @param int $stackPtr The stack position of the class.
     *
     * @return string|false
     */
    public function findExtendedClassName($stackPtr)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the position of the first token on a line, matching given type.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types The type(s) of tokens to search for.
     * @param int $start The position to start searching from in the
     *                           token stack. The first token matching on
     *                           this line before this token will be returned.
     * @param bool $exclude If true, find the token that is NOT of
     *                           the types specified in $types.
     * @param string $value The value that the token must be equal to.
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     *
     * @return int | bool
     */
    public function findFirstOnLine($types, $start, $exclude = false, $value = null)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the names of the interfaces that the specified class implements.
     *
     * Returns FALSE on error or if there are no implemented interface names.
     *
     * @param int $stackPtr The stack position of the class.
     *
     * @return array|false
     */
    public function findImplementedInterfaceNames($stackPtr)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the position of the next specified token(s).
     *
     * If a value is specified, the next token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types The type(s) of tokens to search for.
     * @param int $start The position to start searching from in the
     *                           token stack.
     * @param int $end The end position to fail if no token is found.
     *                           if not specified or null, end will default to
     *                           the end of the token stack.
     * @param bool $exclude If true, find the next token that is NOT of
     *                           a type specified in $types.
     * @param string $value The value that the token(s) must be equal to.
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     * @param bool $local If true, tokens outside the current statement
     *                           will not be checked. i.e., checking will stop
     *                           at the next semi-colon found.
     *
     * @return int|bool
     * @see    findPrevious()
     */
    public function findNext(
        $types,
        $start,
        $end = null,
        $exclude = false,
        $value = null,
        $local = false
    ) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the position of the previous specified token(s).
     *
     * If a value is specified, the previous token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types The type(s) of tokens to search for.
     * @param int $start The position to start searching from in the
     *                           token stack.
     * @param int $end The end position to fail if no token is found.
     *                           if not specified or null, end will default to
     *                           the start of the token stack.
     * @param bool $exclude If true, find the previous token that is NOT of
     *                           the types specified in $types.
     * @param string $value The value that the token(s) must be equal to.
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     * @param bool $local If true, tokens outside the current statement
     *                           will not be checked. IE. checking will stop
     *                           at the previous semi-colon found.
     *
     * @return int|bool
     * @see    findNext()
     */
    public function findPrevious(
        $types,
        $start,
        $end = null,
        $exclude = false,
        $value = null,
        $local = false
    ) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the position of the first non-whitespace token in a statement.
     *
     * @param int $start The position to start searching from in the token stack.
     * @param int|array $ignore Token types that should not be considered stop points.
     *
     * @return int
     */
    public function findStartOfStatement($start, $ignore = null)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the original file for this decorator
     *
     * @return File The original file wrapper.
     */
    public function getBaseFile(): File
    {
        return $this->baseFile;
    }

    /**
     * Returns the visibility and implementation properties of a class.
     *
     * The format of the array is:
     * <code>
     *   array(
     *    'is_abstract' => false, // true if the abstract keyword was found.
     *    'is_final'    => false, // true if the final keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_CLASS token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\TokenizerException If the specified position is not a
     *                                                        T_CLASS token.
     */
    public function getClassProperties($stackPtr)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Return the position of the condition for the passed token.
     *
     * Returns FALSE if the token does not have the condition.
     *
     * @param int $stackPtr The position of the token we are checking.
     * @param int $type The type of token to search for.
     * @param bool $first If TRUE, will return the matched condition
     *                    furtherest away from the passed token.
     *                    If FALSE, will return the matched condition
     *                    closest to the passed token.
     *
     * @return int
     */
    public function getCondition($stackPtr, $type, $first = true)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the declaration names for classes, interfaces, traits, and functions.
     *
     * @param int $stackPtr The position of the declaration token which
     *                      declared the class, interface, trait, or function.
     *
     * @return string|null The name of the class, interface, trait, or function;
     *                     or NULL if the function or class is anonymous.
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not of type
     *                                                      T_FUNCTION, T_CLASS, T_ANON_CLASS,
     *                                                      T_CLOSURE, T_TRAIT, or T_INTERFACE.
     */
    public function getDeclarationName($stackPtr)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the number of errors raised.
     *
     * @return int
     */
    public function getErrorCount()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the errors raised from processing this file.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the absolute filename of this file.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the number of fixable errors/warnings raised.
     *
     * @return int
     */
    public function getFixableCount()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the number of fixed errors/warnings.
     *
     * @return int
     */
    public function getFixedCount()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the list of ignored lines.
     *
     * @return array
     */
    public function getIgnoredLines()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the visibility and implementation properties of the class member
     * variable found at the specified position in the stack.
     *
     * The format of the array is:
     *
     * <code>
     *   array(
     *    'scope'           => 'public', // public protected or protected.
     *    'scope_specified' => false,    // true if the scope was explicitely specified.
     *    'is_static'       => false,    // true if the static keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_VARIABLE token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\TokenizerException If the specified position is not a
     *                                                        T_VARIABLE token, or if the position is not
     *                                                        a class member variable.
     */
    public function getMemberProperties($stackPtr)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the method parameters for the specified function token.
     *
     * Each parameter is in the following format:
     *
     * <code>
     *   0 => array(
     *         'name'              => '$var',  // The variable name.
     *         'token'             => integer, // The stack pointer to the variable name.
     *         'content'           => string,  // The full content of the variable definition.
     *         'pass_by_reference' => boolean, // Is the variable passed by reference?
     *         'variable_length'   => boolean, // Is the param of variable length through use of `...` ?
     *         'type_hint'         => string,  // The type hint for the variable.
     *         'type_hint_token'   => integer, // The stack pointer to the type hint
     *                                         // or false if there is no type hint.
     *         'nullable_type'     => boolean, // Is the variable using a nullable type?
     *        )
     * </code>
     *
     * Parameters with default values have an additional array index of
     * 'default' with the value of the default as a string.
     *
     * @param int $stackPtr The position in the stack of the function token
     *                      to acquire the parameters for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\TokenizerException If the specified $stackPtr is not of
     *                                                        type T_FUNCTION or T_CLOSURE.
     */
    public function getMethodParameters($stackPtr)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }


    /**
     * Returns the visibility and implementation properties of a method.
     *
     * The format of the array is:
     * <code>
     *   array(
     *    'scope'                => 'public', // public protected or protected
     *    'scope_specified'      => true,     // true is scope keyword was found.
     *    'return_type'          => '',       // the return type of the method.
     *    'return_type_token'    => integer,  // The stack pointer to the start of the return type
     *                                        // or false if there is no return type.
     *    'nullable_return_type' => false,    // true if the return type is nullable.
     *    'is_abstract'          => false,    // true if the abstract keyword was found.
     *    'is_final'             => false,    // true if the final keyword was found.
     *    'is_static'            => false,    // true if the static keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the function token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\TokenizerException If the specified position is not a
     *                                                        T_FUNCTION token.
     */
    public function getMethodProperties($stackPtr)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the metrics found while processing this file.
     *
     * @return array
     */
    public function getMetrics()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the number of successes recorded.
     *
     * @return int
     */
    public function getSuccessCount()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the token stack for this file.
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the content of the tokens from the specified start position in
     * the token stack for the specified length.
     *
     * @param int $start The position to start from in the token stack.
     * @param int $length The length of tokens to traverse from the start pos.
     * @param int $origContent Whether the original content or the tab replaced
     *                         content should be used.
     *
     * @return string The token contents.
     */
    public function getTokensAsString($start, $length, $origContent = false)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the number of warnings raised.
     *
     * @return int
     */
    public function getWarningCount()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Returns the warnings raised from processing this file.
     *
     * @return array
     */
    public function getWarnings()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Determine if the passed token has a condition of one of the passed types.
     *
     * @param int $stackPtr The position of the token we are checking.
     * @param int|array $types The type(s) of tokens to search for.
     *
     * @return boolean
     */
    public function hasCondition($stackPtr, $types)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Determine if the passed token is a reference operator.
     *
     * Returns true if the specified token position represents a reference.
     * Returns false if the token represents a bitwise operator.
     *
     * @param int $stackPtr The position of the T_BITWISE_AND token.
     *
     * @return boolean
     */
    public function isReference($stackPtr)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Tokenizes the file and prepares it for the test run.
     *
     * @return void
     */
    public function parse()
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Starts the stack traversal and tells listeners when tokens are found.
     *
     * @return void
     */
    public function process()
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Record a metric about the file being examined.
     *
     * @param int $stackPtr The stack position where the metric was recorded.
     * @param string $metric The name of the metric being recorded.
     * @param string $value The value of the metric being recorded.
     *
     * @return boolean
     */
    public function recordMetric($stackPtr, $metric, $value)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Reloads the content of the file.
     *
     * By default, we have no idea where our content comes from, so we can't do anything.
     *
     * @return void
     */
    public function reloadContent()
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * Set the content of the file.
     *
     * Setting the content also calculates the EOL char being used.
     *
     * @param string $content The file content.
     *
     * @return void
     */
    public function setContent($content)
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * We need to clone the properties of the base file to this. A magic getter on inherited props does not work.
     *
     * @param File $baseFile
     *
     * @return void
     */
    private function takeProperties(File $baseFile)
    {
        $baseProps = get_object_vars($baseFile);

        array_walk($baseProps, function ($value, $key) {
            $this->$key = $value;
        });
    }
}