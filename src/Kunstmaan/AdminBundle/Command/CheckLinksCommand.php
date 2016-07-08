<?php

namespace Kunstmaan\AdminBundle\Command;


use Kunstmaan\AdminBundle\Helper\UrlChecker\DeadLinkFinder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckLinksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kuma:deadlink:check')
            ->setDescription('Checks link sources for dead links')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var DeadLinkFinder $deadlinkFinder */
        $deadlinkFinder = $this->getContainer()->get('kunstmaan_admin.deadlink_finder');
        $deadlinkFinder->setOutput($output);
        $deadlinkFinder->run();
    }
}