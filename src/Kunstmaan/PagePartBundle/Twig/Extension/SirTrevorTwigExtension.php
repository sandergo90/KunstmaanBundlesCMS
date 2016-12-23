<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;

/**
 * SirTrevorTwigExtension
 */
class SirTrevorTwigExtension extends \Twig_Extension
{
    const BASE_TEMPLATE = 'KunstmaanPagePartBundle:SirTrevor:base.html.twig';
    const RENDER_TEMPLATE = 'KunstmaanPagePartBundle:SirTrevor:render.html.twig';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sir_trevor', [$this, 'sirTrevor'], [
                'needs_context' => true,
                'needs_environment' => true,
                'is_safe' => ['html']
            ]),
            new \Twig_SimpleFunction('render_editable', [$this, 'renderEditable'], [
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => ['html']
            ]),
            new \Twig_SimpleFunction('get_class', [$this, 'getClass'], [
                'is_safe' => ['html']
            ]),
        );
    }

    /**
     * Render sir trevor defaults
     *
     * @param \Twig_Environment $environment
     * @param                   $context
     * @return string
     */
    public function sirTrevor(\Twig_Environment $environment)
    {
        return $environment->render(self::BASE_TEMPLATE);
    }

    /**
     * @param $object
     *
     * @return string
     */
    public function getClass($object)
    {
        return get_class($object);
    }

    /**
     * @param \Twig_Environment $environment
     * @param array             $twigContext The twig context
     * @param                   $field
     * @return string
     */
    public function renderEditable(\Twig_Environment $environment, array $twigContext, $field)
    {
        $func = "get".ucwords($field);
        $resource = $twigContext['resource'];

        $data = [
            'data' => [[
                'type' => 'text',
                'data' => [
                    'text' => twig_raw_filter($resource->$func()),
                    'format' => 'html'
                ]]
            ]
        ];

        $twigContext = array_merge($twigContext, [
            'data' => json_encode($data)
        ]);

        return $environment->render(self::RENDER_TEMPLATE, $twigContext);
    }
}
