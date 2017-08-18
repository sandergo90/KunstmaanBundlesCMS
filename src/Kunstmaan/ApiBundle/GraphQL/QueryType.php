<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\AdminBundle\GraphQL\Query\User\UsersField;
use Kunstmaan\ApiBundle\Helper\GraphQLHelper;
use Kunstmaan\NodeBundle\GraphQL\Query\Node\NodesField;
use Kunstmaan\NodeBundle\GraphQL\Query\NodeTranslation\NodeTranslationsField;
use Kunstmaan\NodeBundle\GraphQL\Query\Page\ApiPagesField;
use Kunstmaan\NodeBundle\GraphQL\Query\Page\ListPagePartsType;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class QueryType.
 */
class QueryType extends AbstractObjectType
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
        parent::__construct();

        $this->helper = $helper;
    }

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config->addFields([
            new UsersField(),
            new NodesField(),
            new NodeTranslationsField(),
        ]);

        $entities = $this->helper->getEntities();

        /** @var ClassMetadata $entity */
        foreach ($entities as $entity) {
            $config->addField(new ApiPagesField($entity, $this->helper));
        }
    }
}