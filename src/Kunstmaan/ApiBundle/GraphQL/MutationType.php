<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\GraphQL\Mutation\User\UsersMutation;
use Kunstmaan\NodeBundle\GraphQL\Mutation\Page\PagesMutation;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class MutationType.
 */
class MutationType extends AbstractObjectType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $pages;

    /**
     * MutationType constructor.
     *
     * @param EntityManagerInterface $em
     * @param array                  $pages
     */
    public function __construct(EntityManagerInterface $em, array $pages)
    {
        parent::__construct();

        $this->em = $em;
        $this->pages = $pages;
    }


    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config->addFields([
            new UsersMutation(),
        ]);

        foreach ($this->pages as $page) {
            $fields = $this->getFieldTypes($page);
            $config->addField(new PagesMutation($page, $fields));
        }
    }

    /**
     * @param $page
     *
     * @return array
     */
    private function getFieldTypes($page)
    {
        $fields = [];
        $fieldNames = $this->em->getClassMetadata($page)->getFieldNames();

        foreach ($fieldNames as $fieldName) {
            $properties = $this->em->getClassMetadata($page)->getFieldMapping($fieldName);

            $fields[$fieldName] = $properties['type'];
        }

        return $fields;
    }
}