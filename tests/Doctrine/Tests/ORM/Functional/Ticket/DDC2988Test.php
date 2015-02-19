<?php

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\JoinColumns;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @group DDC-2988
 */
class DDC2988Test extends \Doctrine\Tests\OrmFunctionalTestCase
{
    protected $groups;

    /**
     * {@inheritDoc}
     */
    protected function setup()
    {
        parent::setup();

        try {
            $this->_schemaTool->createSchema(array(
                $this->_em->getClassMetadata(__NAMESPACE__ . '\DDC2988User'),
                $this->_em->getClassMetadata(__NAMESPACE__ . '\DDC2988Group'),
            ));
        } catch (\Exception $e) {
            return;
        }

        $group = new DDC2988Group();
        $this->groups[] = $group;
        $this->_em->persist($group);

        $user = new DDC2988User();
        $user->groups[] = $this->groups;
        $this->_em->persist($user);

        $this->_em->flush();
        $this->_em->clear();
//
//        $qualification = new DDC2988Qualification();
//        $qualificationMetadata = new DDC2988QualificationMetadata($qualification);
//
//        $category1 = new DDC2988Category();
//        $category2 = new DDC2988Category();
//
//        $metadataCategory1 = new DDC2988MetadataCategory($qualificationMetadata, $category1);
//        $metadataCategory2 = new DDC2988MetadataCategory($qualificationMetadata, $category2);
//
//        $this->_em->persist($qualification);
//        $this->_em->persist($qualificationMetadata);
//
//        $this->_em->persist($category1);
//        $this->_em->persist($category2);
//
//        $this->_em->persist($metadataCategory1);
//        $this->_em->persist($metadataCategory2);
//
//        $this->_em->flush();
//        $this->_em->clear();
    }


    public function testManyToManyFindBy()
    {
        $userRepository = $this->_em->getRepository(__NAMESPACE__ . '\DDC2988User');
        $result = $userRepository->findBy(array('groups' => $this->groups));
    }
//    public function testCorrectNumberOfAssociationsIsReturned()
//    {
//        $repository = $this->_em->getRepository(__NAMESPACE__ . '\DDC2988Qualification');
//
//        $builder = $repository->createQueryBuilder('q')
//            ->select('q, qm, qmc')
//            ->innerJoin('q.metadata', 'qm')
//            ->innerJoin('qm.metadataCategories', 'qmc');
//
//        $result = $builder->getQuery()
//            ->getArrayResult();
//
//        $this->assertCount(2, $result[0]['metadata']['metadataCategories']);
//    }
}

/** @Entity  @Table(name="ddc_2988_user") */
class DDC2988User
{
    /** @Id @Column(type="integer") @GeneratedValue */
    public $id;

    /** @ManyToMany(targetEntity="DDC2988Group")
     * @JoinTable(name="users_to_groups",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    public $groups;

    public function __contruct()
    {
        $this->groups = new ArrayCollection();
    }
}

/** @Entity  @Table(name="ddc_2988_group") */
class DDC2988Group
{
    /** @Id @Column(type="integer") @GeneratedValue */
    public $id;
}
