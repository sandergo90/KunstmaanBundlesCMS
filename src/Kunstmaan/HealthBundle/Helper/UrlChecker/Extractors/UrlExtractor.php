<?php

namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Extractors;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces\UrlExtractorInterface;

/**
 * Extracts URLs from a string
 */
class UrlExtractor implements UrlExtractorInterface
{
    private $decodeHtml;

    /**
     * If set, local URLs will also be extracted
     * @var string
     */
    private $baseUrl;

    function __construct($decodeHtml = false, $baseUrl = null)
    {
        $this->decodeHtml = $decodeHtml;
        $this->baseUrl = $baseUrl;
    }

    public function extract($str)
    {
        $urls = $this->extractAbsoluteUrls($str);

        if ($this->baseUrl) {
            $urls = array_merge($urls, $this->extractLocalUrls($str));
        }

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

        $baseUrl = $this->baseUrl;
        $urls = array_map(function($path) use ($baseUrl) {
            return $baseUrl . $path;
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

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
}