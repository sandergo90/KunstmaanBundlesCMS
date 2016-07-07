<?php

namespace Kunstmaan\AdminBundle\Helper\UrlChecker;

interface UrlCheckerInterface
{
    /**
     * @param $url
     * @return bool Whether URL is "broken" or not
     */
    public function check($url);
}