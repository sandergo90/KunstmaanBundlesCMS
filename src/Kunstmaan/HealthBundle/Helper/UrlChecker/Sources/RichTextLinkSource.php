<?php

namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Sources;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Extractors\PagePartUrlExtractor;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces\LinkSourceInterface;
use Kunstmaan\HealthBundle\Model\Link;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\URLHelper;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RichTextLinkSource implements LinkSourceInterface
{
    /** @var PagePartCollector */
    protected $collector;

    /** @var PagePartUrlExtractor */
    protected $urlExtractor;

    /** @var URLHelper */
    protected $urlHelper;

    /** @var EntityManagerInterface $em */
    protected $em;

    /** @var array|null */
    private $nodeTranslationMap = null;

    public function __construct(PagePartCollector $collector, PagePartUrlExtractor $urlExtractor, URLHelper $urlHelper, EntityManagerInterface $em)
    {
        $this->collector = $collector;
        $this->urlExtractor = $urlExtractor;
        $this->urlHelper = $urlHelper;
        $this->em = $em;
    }

    public function getLinks()
    {
        $links = [];
        $nodeTranslations = $this->getNodeTranslationMap();

        foreach ($this->collector->getPageParts([TextareaType::class, WysiwygType::class]) as $pagePart) {
            /** @var PagePartRef $ref */
            $ref = $pagePart['ref'];
            /** @var PagePartInterface $entity */
            $entity = $pagePart['entity'];

            $fields = $pagePart['fields'];

            $reflect = new \ReflectionClass($entity);

            $urls = $this->urlExtractor->extractTextFields($entity, $fields);

            if ($urls) {
                /** @var NodeTranslation $nodeTranslation |null */
                foreach ($nodeTranslations as $nodeTranslation) {
                    if ($nodeTranslation['ref_id'] == $ref->getPageId() && $nodeTranslation['ref_entity_name'] == $ref->getPageEntityname()) {
                        foreach ($urls as $url) {
                            $url = $this->urlHelper->replaceUrl($url);
                            $links[] = new Link($url, 'KunstmaanNodeBundle_nodes_edit', array('id' => $nodeTranslation['id']), $reflect->getShortName());
                        }
                        break;
                    }
                }
            }
        }

        return $links;
    }

    /**
     * Get a map of all node translations. Only called once for caching.
     *
     * @return array|null
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getNodeTranslationMap()
    {
        if (is_null($this->nodeTranslationMap)) {
            $sql = "SELECT n.id, nv.node_translation_id as ntid, nv.id as nvid, nv.ref_id, nv.ref_entity_name
                    FROM kuma_node_translations as nt 
                    JOIN kuma_node_versions as nv on nv.id = nt.public_node_version_id
                    JOIN kuma_nodes as n on n.id = nt.node_id
                    WHERE n.deleted = 0
                    AND nt.online = 1";
            $stmt = $this->em->getConnection()->prepare($sql);
            $stmt->execute();
            $this->nodeTranslationMap = $stmt->fetchAll();
        }

        return $this->nodeTranslationMap;
    }
}