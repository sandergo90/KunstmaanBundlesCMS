<?php

namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces;

use Kunstmaan\HealthBundle\Model\Link;

interface LinkSourceInterface
{
    /**
     * @return Link[]
     */
    public function getLinks();
}