<?php
namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces;

interface UrlExtractorInterface
{
    /**
     * @param $str
     * @return array URLs found in $str
     */
    public function extract($str);
}