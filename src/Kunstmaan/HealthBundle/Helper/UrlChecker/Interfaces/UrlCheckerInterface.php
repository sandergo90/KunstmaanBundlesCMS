<?php

namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces;

interface UrlCheckerInterface
{
    /**
     * @param $url
     * @return bool Whether URL is "broken" or not
     */
    public function check($url);
}