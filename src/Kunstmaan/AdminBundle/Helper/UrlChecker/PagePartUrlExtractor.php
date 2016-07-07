<?php

namespace Kunstmaan\AdminBundle\Helper\UrlChecker;

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

            $urls[] = $url;
        }

        return $urls;
    }
}