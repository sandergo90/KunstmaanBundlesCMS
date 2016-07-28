<?php

namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Extractors;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces\UrlExtractorInterface;

/**
 * Extracts URLs from a string
 */
class UrlExtractor implements UrlExtractorInterface
{
    private $decodeHtml;

    function __construct($decodeHtml = false)
    {
        $this->decodeHtml = $decodeHtml;
    }

    public function extract($str)
    {
        $urls = $this->extractAbsoluteUrls($str);

        if ($this->decodeHtml) {
            $this->decodeHtmlUrls($urls);
        }

        return $urls;
    }

    protected function extractAbsoluteUrls($str)
    {
        $pattern = '/https?:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,10}(\/[^"\s]*)?/';

        preg_match_all($pattern, $str, $matches, PREG_PATTERN_ORDER);

        return $matches[0];
    }

    protected function extractLocalUrls($str)
    {
        // we assume HTML links
        $pattern = '/"(\/[^"]+)/';

        preg_match_all($pattern, $str, $matches, PREG_PATTERN_ORDER);

        $urls = array_map(function($path) {
            return $path;
        }, $matches[1]);

        return $urls;
    }

    protected function decodeHtmlUrls(&$urls)
    {
        foreach ($urls as &$url) {
            $url = html_entity_decode($url);
        }
    }

    public function setDecodeHtml($decodeHtml)
    {
        $this->decodeHtml = (bool) $decodeHtml;
    }
}