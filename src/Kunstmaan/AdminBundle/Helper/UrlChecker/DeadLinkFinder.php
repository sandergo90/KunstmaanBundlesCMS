<?php

namespace Kunstmaan\AdminBundle\Helper\UrlChecker;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class DeadLinkFinder
{
    /**
     * @var LinkSourceInterface[]
     */
    protected $linkSources = array();
    /**
     * @var array
     */
    protected $excludePatterns = array();
    /**
     * @var UrlCheckerInterface
     */
    protected $urlChecker;

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(UrlCheckerInterface $urlChecker)
    {
        $this->urlChecker = $urlChecker;
        $this->output = new NullOutput();
    }


    public function addLinkSource(LinkSourceInterface $linkSource)
    {
        $this->linkSources[] = $linkSource;
    }

    public function run()
    {
        if (count($this->linkSources) == 0) {
            $this->output->writeln('No link sources configured');
            return;
        }
        foreach ($this->linkSources as $linkSource) {
            $this->checkLinkSource($linkSource);
        }
    }

    protected function checkLinkSource(LinkSourceInterface $linkSource)
    {
        $this->output->writeln(sprintf('<comment>Checking link source:</comment> <info>%s</info>', get_class($linkSource)));
        $links = $linkSource->getLinks();
        $this->output->writeln(sprintf(' > <info>%d</info> links', count($links)));
        $brokenLinks = array();
        foreach ($links as $link) {
            if (false === $this->checkLink($link)) {
                $brokenLinks[] = $link;
            }
        }
        if (($count = count($brokenLinks)) == 0) {
            return;
        }
        $this->output->writeln(sprintf(' > <comment>%d</comment> dead links found', $count));
    }

    /**
     * @param Link $link
     * @return bool  true if the link is ok, false if the link is broken
     */
    protected function checkLink(Link $link)
    {
        $this->output->write(str_pad(sprintf(' > <info>%s</info> ', $link->getUrl()), 100, '.') . ' ');
        if ($this->isExcluded($link->getUrl())) {
            $this->output->writeln('excluded');
            return true;
        }
        if ($this->urlChecker->check($link->getUrl())) {
            $this->output->writeln('<comment>ok</comment>');
            return true;
        } else {
            $this->output->writeln('<error>broken</error>');
            return false;
        }
    }

    protected function isExcluded($url)
    {
        foreach ($this->excludePatterns as $excludePattern) {
            if (preg_match($excludePattern, $url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @param ProgressBar $progressHelper
     */
    public function setProgressHelper($progressHelper)
    {
        $this->progressHelper = $progressHelper;
    }

    /**
     * @param array $excludePatterns
     */
    public function setExcludePatterns(array $excludePatterns = array())
    {
        $this->excludePatterns = $excludePatterns;
    }

}