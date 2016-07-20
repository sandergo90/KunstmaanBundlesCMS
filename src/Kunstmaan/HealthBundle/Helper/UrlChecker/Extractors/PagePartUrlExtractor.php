<?php

namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Extractors;

use Kunstmaan\AdminBundle\Form\WysiwygType;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces\UrlExtractorInterface;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class PagePartUrlExtractor implements UrlExtractorInterface
{
    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    function __construct()
    {
        $this->propertyAccessor = new PropertyAccessor();
    }

    public function extract($str)
    {
    }

    public function extractFields(PagePartInterface $pagePart, $fields = [])
    {
        $urls = [];

        foreach ($fields as $field) {
            $str = $this->propertyAccessor->getValue($pagePart, $field);

            if ($str) {
                $urls[] = $str;
            }
        }

        return $urls;
    }

    public function extractTextFields(PagePartInterface $pagePart, $fields = [])
    {
        $urls = [];

        $strings = $this->extractFields($pagePart, $fields);

        foreach ($strings as $string) {
            $this->extractUrlsFromText($urls, $string);
        }

        return $urls;
    }

    private function extractUrlsFromText(&$urls, $str)
    {
        // The Regular Expression filter
        $pattern = "/\\<a href=\"(.*?)\\\">/";

        preg_match_all($pattern, $str, $matches, PREG_PATTERN_ORDER);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $urls[] = $match;
            }
        }
    }

}