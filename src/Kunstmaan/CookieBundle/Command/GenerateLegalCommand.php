<?php

namespace Kunstmaan\CookieBundle\Command;

use Kunstmaan\CookieBundle\Generator\LegalGenerator;
use Kunstmaan\GeneratorBundle\Command\KunstmaanGenerateCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Class GenerateLegalCommand
 *
 * @package Kunstmaan\CookieBundle\Command
 */
class GenerateLegalCommand extends KunstmaanGenerateCommand
{
    /* @var BundleInterface */
    private $bundle;

    /* @var string */
    private $prefix;

    /** @var string */
    private $demoSite;

    /** @var Filesystem */
    private $fileSystem;

    /** @var RegistryInterface */
    private $registry;

    /**
     * GenerateLegalCommand constructor.
     *
     * @param Filesystem        $fileSystem
     * @param RegistryInterface $registry
     */
    public function __construct(Filesystem $fileSystem, RegistryInterface $registry)
    {
        parent::__construct();

        $this->fileSystem = $fileSystem;
        $this->registry = $registry;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setDescription('Generates the legal structure')
            ->setHelp(
                <<<EOT
The <info>kuma:generate:legal</info> command generates a basic legal structure.
This will include some extra pages that will be available.

<info>php bin/console kuma:generate:legal</info>

Use the <info>--namespace</info> option to indicate for which bundle you want to create the legal structure

<info>php bin/console kuma:generate:legal --namespace=Namespace/NamedBundle</info>
EOT
            )
            ->addOption(
                'namespace',
                '',
                InputOption::VALUE_OPTIONAL,
                'The namespace of the bundle where we need to create the legal structure in'
            )
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities')
            ->addOption('demosite', '', InputOption::VALUE_NONE, 'Pass this parameter when the demosite styles/javascipt/pages should be generated')
            ->setName('kuma:generate:legal');
    }

    /**
     * {@inheritdoc}
     */
    protected function getWelcomeText()
    {
        return 'Welcome to the Kunstmaan legal generator';
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute()
    {
        $this->assistant->writeSection('Legal structure generation');

        $rootDir = $this->getApplication()->getKernel()->getRootDir().'/../';
        $this->createGenerator()->generate($this->bundle, $this->prefix, $rootDir, $this->assistant->getOption('demosite'));

        $this->assistant->writeSection('Legal structure successfully created', 'bg=green;fg=black');
    }

    /**
     * {@inheritdoc}
     */
    protected function doInteract()
    {
        $this->assistant->writeLine(["This command helps you to generate a legal structure for your website.\n"]);

        // Ask for which bundle we need to create the structure
        $bundleNamespace = $this->assistant->getOptionOrDefault('namespace');
        $this->bundle = $this->askForBundleName('legal', $bundleNamespace);

        // Ask the database table prefix
        $this->prefix = $this->askForPrefix(null, $this->bundle->getNamespace());

        // If we need to generate the content or only the pages structure
        $this->demoSite = $this->assistant->getOption('demosite');
    }

    /**
     * Get the generator.
     *
     * @return LegalGenerator
     */
    protected function createGenerator()
    {
        return new LegalGenerator($this->fileSystem, $this->registry, '/legal', $this->assistant);
    }
}
