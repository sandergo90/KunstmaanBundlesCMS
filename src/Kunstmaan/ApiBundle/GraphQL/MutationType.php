<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Kunstmaan\AdminBundle\GraphQL\Mutation\User\UsersMutation;
use Kunstmaan\NodeBundle\GraphQL\Mutation\Page\PagesMutation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class MutationType.
 */
class MutationType extends AbstractObjectType
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * MutationType constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct([]);
        $this->container = $container;
    }

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        die('lol');
        $config->addFields([
            new UsersMutation(),
            new PagesMutation(),
        ]);
    }
}