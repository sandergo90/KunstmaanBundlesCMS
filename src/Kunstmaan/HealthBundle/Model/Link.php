<?php

namespace Kunstmaan\HealthBundle\Model;


class Link
{
    /** @var string */
    private $url;

    /** @var string */
    private $route;

    /** @var array */
    private $routeParams;

    function __construct($url, $route, $routeParams)
    {
        $this->url = $url;
        $this->route = $route;
        $this->routeParams = $routeParams;
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
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @param array $routeParams
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = $routeParams;
    }

}