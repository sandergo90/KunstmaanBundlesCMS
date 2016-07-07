<?php

namespace Kunstmaan\AdminBundle\Helper\UrlChecker;


interface LinkSourceInterface
{
    /**
     * @return Link[]
     */
    public function getLinks();
}