<?php

namespace Kunstmaan\CookieBundle\Generator;

use Kunstmaan\GeneratorBundle\Generator\KunstmaanGenerator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Class LegalGenerator
 *
 * @package Kunstmaan\CookieBundle\Generator
 */
class LegalGenerator extends KunstmaanGenerator
{
    /* @var BundleInterface */
    private $bundle;

    /* @var string */
    private $rootDir;

    /* @var string */
    private $prefix;

    /* @var bool */
    private $demosite;

    /**
     * @param BundleInterface $bundle
     * @param string          $prefix
     * @param string          $rootDir
     * @param bool            $demosite
     */
    public function generate(BundleInterface $bundle, string $prefix, string $rootDir, bool $demosite)
    {
        $this->bundle = $bundle;
        $this->prefix = GeneratorUtils::cleanPrefix($prefix);
        $this->rootDir = $rootDir;
        $this->demosite = $demosite;
        $this->skeletonDir = __DIR__.'/../Resources/skeleton/legal';

        $parameters = [
            'namespace' => $this->bundle->getNamespace(),
            'bundle' => $this->bundle,
            'bundle_name' => $this->bundle->getName(),
            'prefix' => $this->prefix,
            'demosite' => $this->demosite,
        ];

        $this->generateAssets();
        $this->generateEntities($parameters);
        $this->generateFormTypes($parameters);
        $this->generatePagepartConfigs($parameters);
        $this->generatePagetemplateConfigs($parameters);
        $this->generateTemplates($parameters);

        if ($this->demosite) {
            $this->generateFixtures($parameters);
        }
    }

    /**
     * Generate the ui asset files.
     */
    private function generateAssets()
    {
        $sourceDir = $this->skeletonDir;
        $targetDir = $this->bundle->getPath();

        $relPath = '/Resources/ui/';
        $this->copyFiles($sourceDir.$relPath, $targetDir.$relPath, true);

    }

    /**
     * Generate the entity classes.
     *
     * @param array $parameters The template parameters
     */
    public function generateEntities(array $parameters)
    {
        $relPath = '/Entity/Pages/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'LegalFolderPage.php', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalPage.php', $parameters);

        // Update homepage to add the Legal Folder Page as child.
        $homePage = $this->bundle->getPath().'/Entity/Pages/HomePage.php';
        $phpCode = "            array(\n";
        $phpCode .= "                'name' => 'Legal folder page',\n";
        $phpCode .= "                'class' => '".
            $this->bundle->getNamespace().
            "\\Entity\\Pages\\LegalFolderPage'\n";
        $phpCode .= "            ),";

        if (file_exists($homePage)) {
            // Fist convert to long array syntax for replacement.
            $converter = new ArraySyntaxConverter();
            $converter->revert($homePage);

            $data = file_get_contents($homePage);
            $data = preg_replace(
                '/(function\s*getPossibleChildTypes\s*\(\)\s*\{\s*return\s*array\s*\()/',
                "$1\n$phpCode",
                $data
            );

            file_put_contents($homePage, $data);
            // Put everything to short syntax.
            $converter->convert($homePage);
        }

        $this->assistant->writeLine('Generating pages : <info>OK</info>');

        $relPath = '/Entity/PageParts/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'LegalCenteredIconPagePart.php', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalTipPagePart.php', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalIconTextPagePart.php', $parameters);

        $this->assistant->writeLine('Generating pageparts : <info>OK</info>');
    }

    /**
     * Generate the form type classes.
     *
     * @param array $parameters The template parameters
     */
    public function generateFormTypes(array $parameters)
    {
        // Pages
        $relPath = '/Form/Pages/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'LegalFolderPageAdminType.php', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalPageAdminType.php', $parameters);

        $this->assistant->writeLine('Generating pages form types : <info>OK</info>');

        // PageParts
        $relPath = '/Form/PageParts/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'LegalCenteredIconPagePartAdminType.php', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalTipPagePartAdminType.php', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalIconTextPagePartAdminType.php', $parameters);

        $this->assistant->writeLine('Generating pageparts form types : <info>OK</info>');
    }

    /**
     * Generate the pagepart section configuration.
     *
     * @param array $parameters The template parameters
     */
    public function generatePagepartConfigs(array $parameters)
    {
        $relPath = '/Resources/config/pageparts/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'legal_header.yml', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'legal_main.yml', $parameters);

        $this->assistant->writeLine('Generating pagepart configuration : <info>OK</info>');
    }

    /**
     * Generate the page template configuration.
     *
     * @param array $parameters The template parameters
     */
    public function generatePagetemplateConfigs(array $parameters)
    {
        $relPath = '/Resources/config/pagetemplates/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'legalpage.yml', $parameters);

        $this->assistant->writeLine('Generating pagetemplate configuration : <info>OK</info>');
    }

    /**
     * Generate the twig templates.
     *
     * @param array $parameters The template parameters
     */
    public function generateTemplates(array $parameters)
    {
        // Pages
        $relPath = '/Resources/views/Pages/LegalPage/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'pagetemplate.html.twig', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'view.html.twig', $parameters);

        $relPath = '/Resources/views/PageParts/LegalCenteredIconPagePart/';
        $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, $parameters, true);
        $relPath = '/Resources/views/PageParts/LegalTipPagePart/';
        $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, $parameters, true);
        $relPath = '/Resources/views/PageParts/LegalIconTextPagePart/';
        $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, $parameters, true);

        $this->assistant->writeLine('Generating template files : <info>OK</info>');
    }

    /**
     * Generate the data fixtures classes.
     *
     * @param array $parameters The template parameters
     */
    public function generateFixtures(array $parameters)
    {
        $relPath = '/DataFixtures/ORM/LegalGenerator/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'DefaultFixtures.php', $parameters);

        $this->assistant->writeLine('Generating fixtures : <info>OK</info>');
    }
}
