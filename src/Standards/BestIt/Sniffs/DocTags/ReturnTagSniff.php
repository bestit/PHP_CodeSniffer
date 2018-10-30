<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\CodeWarning;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function explode;
use function in_array;
use function strtolower;

/**
 * Class ReturnTagSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class ReturnTagSniff extends AbstractTagSniff
{
    /**
     * Code when no array-return-type found.
     *
     * @var string
     */
    public const CODE_NO_ARRAY_FOUND = 'NoArrayFound';

    /**
     * Code when nullable return-type is not described in doc-block.
     *
     * @var string
     */
    public const CODE_NULLABLE_RETURN_FOUND = 'NullableReturnFound';

    /**
     * Code that the tag content format is invalid.
     *
     * @var string
     */
    public const CODE_TAG_MISSING_RETURN_DESC = 'MissingReturnDescription';

    /**
     * Error code for the mixed type.
     *
     * @var string
     */
    public const CODE_TAG_MIXED_TYPE = 'MixedType';

    /**
     * Code when the return tag is not equal to the return type.
     *
     * @var string
     */
    public const CODE_TAG_NOT_EQUAL_TO_RETURN_TYPE = 'NotEqualToReturnType';

    /**
     * Message when no array-return-type found.
     *
     * @var string
     */
    private const MESSAGE_CODE_NO_ARRAY_FOUND = 'Your doc block describes an array return. ' .
    'But there was no ": array" found at the end of your method';

    /**
     * Message when nullable return-type is not described in doc-block.
     *
     * @var string
     */
    private const MESSAGE_CODE_NULLABLE_RETURN_FOUND = 'Your return-type is nullable. You should add this to your doc.';

    /**
     * Message that the tag content format is invalid.
     *
     * @var string
     */
    private const MESSAGE_CODE_TAG_MISSING_RETURN_DESC = 'Are you sure that you do not want to describe your return?';

    /**
     * Message for not equal return types.
     *
     * @var string
     */
    private const MESSAGE_CODE_TAG_NOT_EQUAL_TO_RETURN_TYPE = 'Your return-type is not equal to your return-tag ' .
    'in method description';

    /**
     * The message for the mixed type warning.
     *
     * @var string
     */
    private const MESSAGE_TAG_MIXED_TYPE = 'We suggest that you avoid the "mixed" type and declare the ' .
    'required types in detail.';

    /**
     * This return types will not need a summary in any case.
     *
     * @var array
     */
    public $excludedTypes = ['void'];

    /**
     * The token including the current return-type.
     *
     * @var array $returnType
     */
    private $returnType;

    /**
     * The return types in the doc-block.
     *
     * @var array $tagTypes
     */
    private $tagTypes;

    /**
     * Check if the array return types are correctly described.
     *
     * @return void
     */
    private function checkArrayReturnTypes(): void
    {
        if ($this->checkIfReturnTagsContainAnArray() && $this->returnType['content'] !== 'array') {
            $this->file->addWarning(
                self::MESSAGE_CODE_NO_ARRAY_FOUND,
                $this->returnType['position'],
                static::CODE_NO_ARRAY_FOUND
            );
        }
    }

    /**
     * Check if the return tag is describing array.
     *
     * @return bool
     */
    private function checkIfReturnTagsContainAnArray(): bool
    {
        $isArray = false;

        foreach ($this->tagTypes as $type) {
            if (strpos($type, '[]')) {
                $isArray = true;
            }
        }

        return $isArray;
    }

    /**
     * Check if the normal return types are equal.
     *
     * @return void
     */
    private function checkNormalReturnTypes(): void
    {
        if (!in_array($this->returnType['content'], $this->tagTypes, true) &&
            $this->returnType['content'] !== 'array'
        ) {
            $isFixing = $this->file->addWarning(
                self::MESSAGE_CODE_TAG_NOT_EQUAL_TO_RETURN_TYPE,
                $this->returnType['position'],
                static::CODE_TAG_NOT_EQUAL_TO_RETURN_TYPE
            );

            if ($isFixing) {
                $this->fixSimpleTypes();
            }
        }
    }

    /**
     * Check if the nullable retun types are correctly described.
     *
     * @return void
     */
    private function checkNullableReturnTypes(): void
    {
        if (strpos($this->returnType['content'], '?') !== false) {
            if (!in_array('null', $this->tagTypes, true)) {
                $this->file->addWarning(
                    self::MESSAGE_CODE_NULLABLE_RETURN_FOUND,
                    $this->returnType['position'],
                    static::CODE_NULLABLE_RETURN_FOUND
                );
            }
        }
    }

    /**
     * Find the return type of a method.
     *
     * @return array|null The return-type token, if found.
     */
    private function findReturnType(): ?array
    {
        $nextFunctionPos = $this->file->findNext([T_FUNCTION], $this->stackPos);
        $nextFunction = $this->file->getTokens()[$nextFunctionPos];

        $parenthesisCloserPos = $nextFunction['parenthesis_closer'];

        $nxtPrthesisCloserPos = $this->file->findNext(
            [T_WHITESPACE],
            $parenthesisCloserPos + 1,
            null,
            true
        );

        $nxtPrthesisCloser = $this->file->getTokens()[$nxtPrthesisCloserPos];
        $nextReturnType = null;

        if ($nxtPrthesisCloser['code'] === T_COLON) {
            $nextReturnTypePos = $this->file->findNext(
                [T_WHITESPACE],
                $nxtPrthesisCloserPos + 1,
                null,
                true
            );

            $nextReturnType = $this->file->getTokens()[$nextReturnTypePos];
            $nextReturnType['position'] = $nextReturnTypePos;

            if ($nextReturnType['code'] === T_NULLABLE) {
                $nextReturnType = $this->file->getTokens()[$nextReturnTypePos + 1];
                $nextReturnType['content'] = '?' . $nextReturnType['content'];
                $nextReturnType['position'] = $nextReturnTypePos;
            }
        }

        return $nextReturnType['code'] === T_STRING ? $nextReturnType : null;
    }

    /**
     * Fix the simple return types.
     *
     * @return void
     */
    private function fixSimpleTypes(): void
    {
        $originDesc = $this->file->getTokens()[$this->stackPos + 2]['content'];

        $originDesc = strstr($originDesc, ' ', false);

        $this->file->fixer->beginChangeset();

        $this->file->fixer->replaceToken(
            $this->stackPos + 2,
            $this->returnType['content'] . '' . $originDesc
        );

        $this->file->fixer->endChangeset();
    }

    /**
     * Processed the content of the required tag.
     *
     * @param null|string $tagContent The possible tag content or null.
     * @throws CodeWarning If there is a mixed type.
     *
     * @return void
     */
    protected function processTagContent(?string $tagContent = null): void
    {
        $returnParts = explode(' ', (string) $tagContent);
        $type = $returnParts[0];

        if (strtolower($type) === 'mixed') {
            throw (new CodeWarning(static::CODE_TAG_MIXED_TYPE, self::MESSAGE_TAG_MIXED_TYPE, $this->stackPos))
                ->setToken($this->token);
        }

        if (!in_array($type, $this->excludedTypes) && count($returnParts) <= 1) {
            $this->file->addWarning(
                self::MESSAGE_CODE_TAG_MISSING_RETURN_DESC,
                $this->stackPos,
                static::CODE_TAG_MISSING_RETURN_DESC
            );
        }

        $this->returnType = $this->findReturnType();

        if ($this->returnType) {
            if (TypeHintHelper::isSimpleTypeHint($this->returnType['content'])) {
                $this->tagTypes = explode('|', $type);

                $this->checkNormalReturnTypes();
                $this->checkArrayReturnTypes();
            } elseif (strpos($this->returnType['content'], '?') !== false) {
                if (TypeHintHelper::isSimpleTypeHint(str_replace('?', '', $this->returnType['content']))) {
                    $this->tagTypes = explode('|', $type);
                    $this->checkNullableReturnTypes();
                }
            }
        }
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    protected function registerTag(): string
    {
        return 'return';
    }
}
