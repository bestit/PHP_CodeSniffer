<?php

declare(strict_types = 1);

namespace BestIt\Sniffs\Imports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Class ImportSniff.
 * @package BestIt\Sniffs\Imports
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 */
final class ImportSniff implements Sniff
{
    /**
     * Namespace separator count.
     *
     * @var int
     */
    private $nsSeperatorCount = 0;

    /**
     * Code when FQN is found.
     *
     * @var string
     */
    public const CODE_FQN_FOUND = 'FQNFound';

    /**
     * Warning message if a FQN is found.
     *
     * @var string
     */
    public const ERROR_FQN_NOT_ALLOWED = 'FQN not allowed!';

    /**
     * Register.
     *
     * @return int[]
     */
    public function register(): array
    {
        return [
            T_USE,
            T_NS_SEPARATOR
        ];
    }

    /**
     * Check if there is any FQN.
     *
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $tokens = $file->getTokens();
        $token = $tokens[$position];

        if ($token['type'] === 'T_USE') {
            $count = 0;

            for ($i = $position; $i <= $file->findEndOfStatement($position, [T_COMMA]); $i++) {
                if ($tokens[$i]['type'] === 'T_NS_SEPARATOR') {
                    $count++;
                }
            }

            $this->nsSeperatorCount -= $count;
        } elseif ($token['type'] === 'T_NS_SEPARATOR') {
            $this->nsSeperatorCount++;
        }

        if($this->nsSeperatorCount > 0){
            $file->addError(
                self::ERROR_FQN_NOT_ALLOWED,
                $position,
                self::CODE_FQN_FOUND
            );
        }
    }
}
