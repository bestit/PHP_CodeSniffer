<?php

namespace BestIt\Sniffs\DocTags\Fixtures\TagSortingSniff\Correct;

class ORMJoinsOnMethods {
    /**
     * This is a test method.
     *
     * @ORM\JoinTable(name="bestit_genius_offering_related_article",
     *      joinColumns={
     *          @ORM\JoinColumn(
     *              name="offering",
     *              referencedColumnName="id"
     *          )
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(
     *              name="article",
     *              referencedColumnName="id"
     *          )
     *      }
     * )
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Article\Detail")
     * @throws RuntimeException
     *
     * @param string $param1
     * @param string $param2
     *
     * @todo Test1
     * @todo Test2
     * @todo Test3
     *
     * @return void
     */
    public function test(string $param1, string $param2): void
    {
        throw new RuntimeException('To be implemented');
    }
}