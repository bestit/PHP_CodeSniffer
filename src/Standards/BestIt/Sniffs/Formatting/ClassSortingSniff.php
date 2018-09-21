<?php

namespace BestIt\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Sniff for the sorting of a class.
 * The standard sorting is:
 *  1. Constants
 *  2. Properties
 *  3. Constructor
 *  4. Destructor
 *  5. Methods
 * All entries above are also sorted by visibility and alphabetical.
 *
 * This sniff checks if your class corresponds to this sorting.
 *
 * @package BestIt\Sniffs\Formatting
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 */
final class ClassSortingSniff implements Sniff
{
    /**
     * Error code.
     *
     * @var string
     */
    CONST CODE_WRONG_SORTING_FOUND = 'WrongSortingFound';

    /**
     * Error message.
     *
     * @var string
     */
    CONST ERROR_WRONG_CLASS_SORTING = 'Wrong sorting';

    /**
     * Order from types.
     *
     * @var array
     */
    public $orderTypes = ['T_CONST', 'T_VARIABLE', 'T_FUNCTION-CONSTRUCT', 'T_FUNCTION-DESTRUCT', 'T_FUNCTION'];

    /**
     * Order from visibilities.
     *
     * @var array
     */
    public $orderVisibility = ['T_PRIVATE', 'T_PROTECTED', 'T_PUBLIC'];

    /**
     * Throw an error when a wrong sorting is detected.
     *
     * @param File $file
     * @param int $position
     *
     * @return boolean
     */
    private static function throwFixableError($file, $position)
    {
        return $file->addFixableError(
            self::ERROR_WRONG_CLASS_SORTING,
            $position,
            self::CODE_WRONG_SORTING_FOUND
        );
    }

    /**
     * Register tokens.
     *
     * @return array
     */
    public function register(): array
    {
        return [
            T_CLASS
        ];
    }

    /**
     * Sort the detected Tokens by the schema defined in $orderTypes and $orderVisibility.
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function sort($a, $b): int
    {
        $result = $this->typeSort($a, $b);
        if ($result === 0) {
            $result = $this->visibilitySort($a, $b);
            if ($result === 0) {
                $result = strcasecmp($a['name'], $b['name']);
            }
        }
        return $result;
    }

    /**
     * Sort all type tokens.
     * At the return value the usort() function can see if the entry has to be moved up or down.
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function typeSort($a, $b): int
    {
        return array_search($a['type'], $this->orderTypes, true) - array_search($b['type'], $this->orderTypes, true);
    }

    /**
     * Sort all visibility tokens.
     * At the return value the usort() function can see if the entry has to be moved up or down.
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function visibilitySort($a, $b): int
    {
        return array_search($a['visibility'], $this->orderVisibility, true) - array_search($b['visibility'], $this->orderVisibility, true);
    }

    /**
     * This method checks the whole class and write all CONST-, visibility- and function-tokens in an array.
     * This array will be sorted with the sort() method. After that the original array and the sorted array will be compared.
     * If there are any differences an error will be thrown.
     *
     * @param File $file
     * @param int $position
     *
     * @return void
     */
    public function process(File $file, $position)
    {
        $tokens = $file->getTokens();

        $endPosition = $file->findEndOfStatement($position);

        $classElements = [];
        $functionStart = -1;
        $functionEnd = -1;

        $lastVisibility = null;
        $lastVisibilityEnd = -1;

        for ($i = $position; $i < $endPosition; $i++){
            if ($tokens[$i]['type'] === 'T_CONST') {
                $classElements[] = [
                    'type' => 'T_CONST',
                    'visibility' => $i <= $lastVisibilityEnd ? $lastVisibility : 'T_PUBLIC',
                    'name' => $tokens[$file->findNext(T_STRING, $i)]['content']
                ];
            } elseif($tokens[$i]['type'] === 'T_FUNCTION'){
                $type = 'T_FUNCTION';
                $name = $tokens[$file->findNext(T_STRING, $i)]['content'];
                if (strcasecmp($name, '__destruct') === 0){
                    $type .= '-DESTRUCT';
                } elseif (strcasecmp($name, '__construct') === 0){
                    $type .= '-CONSTRUCT';
                }

                $classElements[] = [
                    'type' => $type,
                    'visibility' => $i <= $lastVisibilityEnd ? $lastVisibility : 'T_PUBLIC',
                    'name' => $name
                ];
                $functionStart = $i;
                $functionEnd = $file->findEndOfStatement($i);
            } elseif ($tokens[$i]['type'] === 'T_VARIABLE' && ($i < $functionStart || $i > $functionEnd)){
                $classElements[] = [
                    'type' => 'T_VARIABLE',
                    'visibility' => $i <= $lastVisibilityEnd ? $lastVisibility : 'T_PUBLIC',
                    'name' => $tokens[$i]['content']
                ];
            } elseif (in_array($tokens[$i]['type'], ['T_PRIVATE', 'T_PROTECTED', 'T_PUBLIC'])){
                $lastVisibility = $tokens[$i]['type'];
                $lastVisibilityEnd = $file->findEndOfStatement($i);
            }
        }

        $originalClassElements = $classElements;
        usort($classElements, [$this, 'sort']);

        if ($classElements !== $originalClassElements){
            self::throwFixableError($file, $position);
        }
    }
}
