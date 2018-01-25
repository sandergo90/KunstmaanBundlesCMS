<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use {{ namespace }}\Form\Pages\LegalPageAdminType;

/**
 * LegalFolderPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}legal_pages")
 */
class LegalPage extends AbstractPage implements HasPageTemplateInterface
{
    /**
     * Returns the default backend form type for this page
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return LegalPageAdminType::class;
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return [
            '{{ bundle.getName() }}:legal_header',
            '{{ bundle.getName() }}:legal_main',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return array('{{ bundle.getName() }}:legalpage');
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle.getName() }}:Pages\LegalPage:view.html.twig';
    }
}
