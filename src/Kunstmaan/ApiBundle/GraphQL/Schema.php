<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Doctrine\ORM\EntityManagerInterface;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Config\Schema\SchemaConfig;

class Schema extends AbstractSchema
{
    /**
     * @var MutationType
     */
    private $mutation;

    /**
     * Schema constructor.
     *
     * @param MutationType $mutation
     */
    public function __construct(MutationType $mutation)
    {
        $this->mutation = $mutation;
        parent::__construct([]);
    }


    public function build(SchemaConfig $config)
    {
        $config
            ->setQuery(new QueryType())
            ->setMutation($this->mutation);
    }
}

