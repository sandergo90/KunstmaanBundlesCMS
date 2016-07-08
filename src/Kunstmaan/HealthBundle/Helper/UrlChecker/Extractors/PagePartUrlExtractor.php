<?php

namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Extractors;

use Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces\UrlExtractorInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;

class PagePartUrlExtractor implements UrlExtractorInterface
{
    public function extract($str)
    {
    }

    public function extractFields(PagePartInterface $pagePart, $fields = [])
    {
        $urls = [];

        foreach ($fields as $field) {
            $getter = 'get' . ucfirst($field);
            $url = $pagePart->$getter();

            if ($url) {
                $urls[] = $url;
            }
        }

        return $urls;
    }
}