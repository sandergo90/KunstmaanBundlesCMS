<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Doctrine\ORM\EntityManagerInterface;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Config\Schema\SchemaConfig;

class Schema extends AbstractSchema
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Schema constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct([]);
    }

    public function build(SchemaConfig $config)
    {
        dump($this->container->get('doctrine'));
        die();
        $config
            ->setQuery(new QueryType())
            ->setMutation(new MutationType($this->container));
    }
}

