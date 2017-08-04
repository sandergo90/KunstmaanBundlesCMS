<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\AdminBundle\GraphQL\Mutation\User\UsersMutation;
use Kunstmaan\ApiBundle\Helper\GraphQLHelper;
use Kunstmaan\NodeBundle\GraphQL\Mutation\Page\CreatePagesMutation;
use Kunstmaan\NodeBundle\GraphQL\Mutation\Page\UpdatePagesMutation;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class MutationType.
 */
class MutationType extends AbstractObjectType
{
    /**
     * @var GraphQLHelper
     */
    private $helper;

    /**
     * MutationType constructor.
     *
     * @param GraphQLHelper $helper
     */
    public function __construct(GraphQLHelper $helper)
    {
        $this->helper = $helper;

        parent::__construct();
    }


    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config->addFields([
            new UsersMutation(),
        ]);

        $entities = $this->helper->getEntities();

        /** @var ClassMetadata $entity */
        foreach ($entities as $entity) {
            $config->addField(new CreatePagesMutation($entity, $this->helper));
//            $config->addField(new UpdatePagesMutation($entity, $this->helper));
        }
    }
}