<?php

namespace Kunstmaan\TranslatorBundle\Twig\Extension;

use Kunstmaan\TranslatorBundle\Twig\KunstmaanTwigLexer;
use Symfony\Bridge\Twig\NodeVisitor\TranslationDefaultDomainNodeVisitor;
use Symfony\Bridge\Twig\NodeVisitor\TranslationNodeVisitor;
use Symfony\Bridge\Twig\TokenParser\TransChoiceTokenParser;
use Symfony\Bridge\Twig\TokenParser\TransDefaultDomainTokenParser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extensions\TokenParser\TransTokenParser;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\TwigFilter;

/**
 * Class TranslationExtension
 */
class TranslationExtension extends AbstractExtension
{
    /** @var Request */
    private $request;

    /** @var null|TranslatorInterface */
    private $translator;

    /** @var null|NodeVisitorInterface */
    private $translationNodeVisitor;

    /**
     * TranslationExtension constructor.
     *
     * @param RequestStack              $requestStack
     * @param null|TranslatorInterface  $translator
     * @param null|NodeVisitorInterface $translationNodeVisitor
     */
    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator = null,
        NodeVisitorInterface $translationNodeVisitor = null
    ) {
        $this->request = $requestStack->getMasterRequest();
        $this->translator = $translator;
        $this->translationNodeVisitor = $translationNodeVisitor;
    }


    /**
     * @return null|TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return AbstractTokenParser[]
     */
    public function getTokenParsers()
    {
        return [
            // {% trans %}Symfony is great!{% endtrans %}
            new TransTokenParser(),

            // {% transchoice count %}
            //     {0} There is no apples|{1} There is one apple|]1,Inf] There is {{ count }} apples
            // {% endtranschoice %}
            new TransChoiceTokenParser(),

            // {% trans_default_domain "foobar" %}
            new TransDefaultDomainTokenParser(),
        ];
    }

    /**
     * @return array|\Twig_NodeVisitorInterface[]
     */
    public function getNodeVisitors()
    {
        return [$this->getTranslationNodeVisitor(), new TranslationDefaultDomainNodeVisitor()];
    }

    /**
     * @return TranslationNodeVisitor
     */
    public function getTranslationNodeVisitor()
    {
        return $this->translationNodeVisitor ?: $this->translationNodeVisitor = new TranslationNodeVisitor();
    }

    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter(
                'trans', [$this, 'trans'],
                [
                    'needs_environment' => true,
                ]
            ),
            new TwigFilter(
                'transchoice', [$this, 'transchoice'],
                [
                    'needs_environment' => true,
                ]
            ),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @param string            $message
     * @param array             $arguments
     * @param null              $domain
     * @param null              $locale
     *
     * @return string
     */
    public function trans(\Twig_Environment $environment, $message, array $arguments = [], $domain = null, $locale = null)
    {
        $lexer = new KunstmaanTwigLexer($environment);
        $environment->setLexer($lexer);

        if (null === $this->translator) {
            return strtr($message, $arguments);
        }

        $inlineMode = $this->request->query->getBoolean('inline-trans', false);

        return $this->translator->trans($message, $inlineMode ? [] : $arguments, $domain, $locale);
    }

    /**
     * @param \Twig_Environment $environment
     * @param string            $message
     * @param integer           $count
     * @param array             $arguments
     * @param null              $domain
     * @param null              $locale
     *
     * @return string
     */
    public function transchoice(\Twig_Environment $environment, $message, $count, array $arguments = [], $domain = null, $locale = null)
    {
        $lexer = new KunstmaanTwigLexer($environment);
        $environment->setLexer($lexer);

        if (null === $this->translator) {
            return strtr($message, $arguments);
        }

        $inlineMode = $this->request->query->get('inline-trans', false);

        return $this->translator->transChoice($message, $count, $inlineMode ? [] : array_merge(['%count%' => $count], $arguments), $domain, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kunstmaan_translator';
    }
}
