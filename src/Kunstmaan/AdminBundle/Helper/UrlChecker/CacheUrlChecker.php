<?php

namespace Kunstmaan\AdminBundle\Helper\UrlChecker;

use Doctrine\Common\Cache\Cache;

class CacheUrlChecker implements UrlCheckerInterface
{
    /** @var UrlCheckerInterface */
    private $urlChecker;

    /** @var Cache */
    private $cache;

    /** @var int */
    private $lifetime;

    function __construct(UrlCheckerInterface $urlChecker, Cache $cache, $lifetime = 3600)
    {
        $this->urlChecker = $urlChecker;
        $this->cache = $cache;
        $this->lifetime = (int)$lifetime;
    }

    public function check($url)
    {
        if ($result = $this->cache->fetch($url)) {
            return '1' === $result;
        }

        $ok = $this->urlChecker->check($url);
        // We save the cache entry as a string because cache->fetch() returns false on cache miss
        $this->cache->save($url, $ok ? '1' : '0', $this->lifetime);
        return $ok;
    }

}