<?php

namespace BestIt\Sniffs\DocTags\Fixtures\TagSortingSniff\WithWarning;

class ORMJoinsOnMethod
{
    /**
     * This is a test method.
     *
     * @var Collection Now that we know who you are, I know who I am. I'm not a mistake! It all makes sense! In a comic,
     *      you know how you can tell who the arch-villain's going to be? He's the exact opposite of the hero. And most
     *      times they're friends, like you and me! I should've known way back when... You know why, David? Because of
 *          the kids. They called me Mr Glass.
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Article\Detail")
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
     *
     * @return void
     */
    public function test(string $param1, string $param2)
    {
        throw new RuntimeException('To be implemented');
    }
}