<?php

namespace Kunstmaan\TranslatorBundle\Twig\Extension;

use Symfony\Bridge\Twig\Extension\TranslationExtension as BaseTranslationExtension;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\TwigFilter;

/**
 * Class TranslationExtension
 */
class TranslationExtension extends BaseTranslationExtension
{
    /** @var null|TranslatorInterface */
    private $translator;

    /** @var null|NodeVisitorInterface */
    private $translationNodeVisitor;

    /** @var EngineInterface */
    private $twigEngine;

    /**
     * TranslationExtension constructor.
     *
     * @param EngineInterface           $engineInterface
     * @param TranslatorInterface|null  $translator
     * @param NodeVisitorInterface|null $translationNodeVisitor
     */
    public function __construct(
        EngineInterface $twigEngine,
        TranslatorInterface $translator = null,
        NodeVisitorInterface $translationNodeVisitor = null
    ) {
        parent::__construct($translator, $translationNodeVisitor);

        $this->translator = $translator;
        $this->translationNodeVisitor = $translationNodeVisitor;
        $this->twigEngine = $twigEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('trans', [$this, 'trans']),
            new TwigFilter('transchoice', [$this, 'transchoice']),
        ];
    }

    /**
     * @inheritdoc
     */
    public function trans($message, array $arguments = [], $domain = null, $locale = null)
    {
        $translator = $this->translator;

        if (null === $this->translator) {
            return strtr($message, $arguments);
        }

        $translation = $this->translator->trans($message, $arguments, $domain, $locale);
        $content = sprintf('<inline-trans data-keyword="%s">%s</inline-trans>', $message, $translation);

        return new \Twig_Markup(
            $content,
            'UTF-8'
        );
    }

    /**
     * @inheritdoc
     */
    public function transchoice($message, $count, array $arguments = [], $domain = null, $locale = null)
    {
        if (null === $this->translator) {
            return strtr($message, $arguments);
        }

        return new \Twig_Markup(
            '<inline-trans>'.$this->translator->trans($message, $arguments, $domain, $locale).'</inline-trans>',
            'UTF-8'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kuma_translator';
    }
}
