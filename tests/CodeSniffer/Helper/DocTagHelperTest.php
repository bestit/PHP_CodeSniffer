<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHP_CodeSniffer\Files\File;
use PHPUnit\Framework\TestCase;

/**
 * Tests DocTagHelper
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class DocTagHelperTest extends TestCase
{
    use FileHelperTrait;

    /**
     * The tested object.
     *
     * @var DocTagHelper
     */
    private $fixture;

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $file = $this->getFile(__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/DocTagHelper/ORMJoinsOnMethod.php');

        $this->fixture = new DocTagHelper($file, 23);
    }

    /**
     * Checks if the comment tags are rendered correctly.
     *
     * @return void
     */
    public function testGetCommentTagTokens(): void
    {
        $expectedArray = [
            36 => [
                'content' => '@ORM\\JoinTable(name="bestit_genius_offering_related_article",',
                'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                'type' => 'T_DOC_COMMENT_TAG',
                'line' => 9,
                'column' => 8,
                'length' => 61,
                'level' => 1,
                'conditions' => [16 => 364,],
                'contents' => [
                    42 => [
                        'content' => 'joinColumns={',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 10,
                        'column' => 13,
                        'length' => 13,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    47 => [
                        'content' => '@ORM\\JoinColumn(',
                        'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                        'type' => 'T_DOC_COMMENT_TAG',
                        'line' => 11,
                        'column' => 17,
                        'length' => 16,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    52 => [
                        'content' => 'name="offering",',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 12,
                        'column' => 21,
                        'length' => 16,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    57 => [
                        'content' => 'referencedColumnName="id"',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 13,
                        'column' => 21,
                        'length' => 25,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    62 => [
                        'content' => ')',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 14,
                        'column' => 17,
                        'length' => 1,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    67 => [
                        'content' => '},',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 15,
                        'column' => 13,
                        'length' => 2,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    72 => [
                        'content' => 'inverseJoinColumns={',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 16,
                        'column' => 13,
                        'length' => 20,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    77 => [
                        'content' => '@ORM\\JoinColumn(',
                        'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                        'type' => 'T_DOC_COMMENT_TAG',
                        'line' => 17,
                        'column' => 17,
                        'length' => 16,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    82 => [
                        'content' => 'name="article",',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 18,
                        'column' => 21,
                        'length' => 15,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    87 => [
                        'content' => 'referencedColumnName="id"',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 19,
                        'column' => 21,
                        'length' => 25,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    92 => [
                        'content' => ')',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 20,
                        'column' => 17,
                        'length' => 1,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    97 => [
                        'content' => '}',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 21,
                        'column' => 13,
                        'length' => 1,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    102 => [
                        'content' => ')',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 22,
                        'column' => 8,
                        'length' => 1,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                ],
            ],
            106 => [
                'content' => '@ORM\\ManyToMany(targetEntity="Shopware\\Models\\Article\\Detail")',
                'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                'type' => 'T_DOC_COMMENT_TAG',
                'line' => 23,
                'column' => 8,
                'length' => 62,
                'level' => 1,
                'conditions' => [16 => 364],
                'contents' => []
            ],
            111 => [
                'content' => '@throws',
                'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                'type' => 'T_DOC_COMMENT_TAG',
                'line' => 24,
                'column' => 8,
                'length' => 7,
                'level' => 1,
                'conditions' => [16 => 364],
                'contents' => [
                    114 => [
                        'content' => 'RuntimeException',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 24,
                        'column' => 16,
                        'length' => 16,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ]
                ],
            ],
            118 => [
                'content' => '@var',
                'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                'type' => 'T_DOC_COMMENT_TAG',
                'line' => 25,
                'column' => 8,
                'length' => 4,
                'level' => 1,
                'conditions' => [16 => 364],
                'contents' => [
                    121 => [
                        'content' => 'Collection Now that we know who you are, I know who I am. I\'m not a mistake! ' .
                            'It all makes sense! In a comic,',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 25,
                        'column' => 13,
                        'length' => 108,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    126 => [
                        'content' => 'you know how you can tell who the arch-villain\'s going to be? He\'s the exact ' .
                            'opposite of the hero. And most',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 26,
                        'column' => 13,
                        'length' => 107,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    131 => [
                        'content' => 'times they\'re friends, like you and me! I should\'ve known way back when... ' .
                            'You know why, David? Because of',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 27,
                        'column' => 13,
                        'length' => 106,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                    136 => [
                        'content' => 'the kids. They called me Mr Glass.',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 28,
                        'column' => 13,
                        'length' => 34,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                ],
            ],
            143 => [
                'content' => '@param',
                'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                'type' => 'T_DOC_COMMENT_TAG',
                'line' => 30,
                'column' => 8,
                'length' => 6,
                'level' => 1,
                'conditions' => [16 => 364],
                'contents' => [
                    146 => [
                        'content' => 'string $param1',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 30,
                        'column' => 15,
                        'length' => 14,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                ],
            ],
            150 => [
                'content' => '@param',
                'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                'type' => 'T_DOC_COMMENT_TAG',
                'line' => 31,
                'column' => 8,
                'length' => 6,
                'level' => 1,
                'conditions' => [16 => 364],
                'contents' => [
                    153 => [
                        'content' => 'string $param2',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 31,
                        'column' => 15,
                        'length' => 14,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                ],
            ],
            160 => [
                'content' => '@todo',
                'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                'type' => 'T_DOC_COMMENT_TAG',
                'line' => 33,
                'column' => 8,
                'length' => 5,
                'level' => 1,
                'conditions' => [16 => 364],
                'contents' => [
                    163 => [
                        'content' => 'Test1',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 33,
                        'column' => 14,
                        'length' => 5,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                ],
            ],
            167 => [
                'content' => '@todo',
                'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                'type' => 'T_DOC_COMMENT_TAG',
                'line' => 34,
                'column' => 8,
                'length' => 5,
                'level' => 1,
                'conditions' => [16 => 364],
                'contents' => [
                    170 => [
                        'content' => 'Test2',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 34,
                        'column' => 14,
                        'length' => 5,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                ],
            ],
            174 => [
                'content' => '@todo',
                'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                'type' => 'T_DOC_COMMENT_TAG',
                'line' => 35,
                'column' => 8,
                'length' => 5,
                'level' => 1,
                'conditions' => [16 => 364],
                'contents' => [
                    177 => [
                        'content' => 'Test3',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 35,
                        'column' => 14,
                        'length' => 5,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                ],
            ],
            184 => [
                'content' => '@return',
                'code' => 'PHPCS_T_DOC_COMMENT_TAG',
                'type' => 'T_DOC_COMMENT_TAG',
                'line' => 37,
                'column' => 8,
                'length' => 7,
                'level' => 1,
                'conditions' => [16 => 364],
                'contents' => [
                    187 => [
                        'content' => 'void',
                        'code' => 'PHPCS_T_DOC_COMMENT_STRING',
                        'type' => 'T_DOC_COMMENT_STRING',
                        'line' => 37,
                        'column' => 16,
                        'length' => 4,
                        'level' => 1,
                        'conditions' => [16 => 364],
                    ],
                ],
            ]
        ];

        static::assertSame($expectedArray, $this->fixture->getTagTokens());
    }
}
