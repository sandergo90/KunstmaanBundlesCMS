<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Kunstmaan\AdminBundle\GraphQL\Query\User\UsersField;
use Kunstmaan\NodeBundle\GraphQL\Query\Node\NodesField;
use Kunstmaan\NodeBundle\GraphQL\Query\NodeTranslation\NodeTranslationsField;
use Kunstmaan\NodeBundle\GraphQL\Query\Page\ApiPagesField;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class QueryType.
 */
class QueryType extends AbstractObjectType
{
    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config->addFields([
            new UsersField(),
            new NodesField(),
            new NodeTranslationsField(),
            new ApiPagesField()
        ]);
    }
}