<?php

namespace Kunstmaan\HealthBundle\Helper\UrlChecker;

use Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces\LinkSourceInterface;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces\UrlCheckerInterface;
use Kunstmaan\HealthBundle\Model\Link;
use Symfony\Component\Console\Helper\ProgressBar;

class DeadLinkFinder
{
    /** @var LinkSourceInterface[] */
    protected $linkSources = array();

    /** @var UrlCheckerInterface */
    protected $urlChecker;

    public function __construct(UrlCheckerInterface $urlChecker)
    {
        $this->urlChecker = $urlChecker;
    }

    public function addLinkSource(LinkSourceInterface $linkSource)
    {
        $this->linkSources[] = $linkSource;
    }

    public function run()
    {
        $brokenLinks = array();

        foreach ($this->linkSources as $linkSource) {
            $brokenLinks = array_merge($brokenLinks, $this->checkLinkSource($linkSource));
        }

        return $brokenLinks;
    }

    protected function checkLinkSource(LinkSourceInterface $linkSource)
    {
        $links = $linkSource->getLinks();

        $brokenLinks = array();
        foreach ($links as $link) {
            if (false === $this->checkLink($link)) {
                $brokenLinks[] = $link;
            }
        }

        return $brokenLinks;
    }

    /**
     * @param Link $link
     * @return bool  true if the link is ok, false if the link is broken
     */
    protected function checkLink(Link $link)
    {
        if ($this->urlChecker->check($link->getUrl())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param ProgressBar $progressHelper
     */
    public function setProgressHelper($progressHelper)
    {
        $this->progressHelper = $progressHelper;
    }
}