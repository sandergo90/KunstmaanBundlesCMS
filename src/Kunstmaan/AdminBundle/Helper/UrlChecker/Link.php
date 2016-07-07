<?php

namespace Kunstmaan\AdminBundle\Helper\UrlChecker;


class Link
{
    /** @var string */
    private $url;

    /** @var mixed */
    private $context;

    function __construct($url, $context)
    {
        $this->url = $url;
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }
}