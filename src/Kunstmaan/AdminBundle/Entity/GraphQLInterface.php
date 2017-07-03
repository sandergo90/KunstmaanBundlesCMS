<?php

namespace Kunstmaan\AdminBundle\Entity;

/**
 * Interface GraphQLInterface.
 */
interface GraphQLInterface
{
    /**
     * Get graphQL object type
     *
     * @return string
     */
    public function getGraphQLType();
}
