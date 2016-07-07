<?php
namespace Kunstmaan\AdminBundle\Helper\UrlChecker;

interface UrlExtractorInterface
{
    /**
     * @param $str
     * @return array URLs found in $str
     */
    public function extract($str);
}