<?php

namespace Kunstmaan\TranslatorBundle\Toolbar;

use Kunstmaan\AdminBundle\Helper\Toolbar\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class InlineTranslationsDataCollector
 */
class InlineTranslationsDataCollector extends AbstractDataCollector
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Request
     */
    private $request;

    /**
     * InlineTranslationsDataCollector constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack $requestStack
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
        $this->request = $requestStack->getMasterRequest();
    }

    /**
     * @return array
     */
    public function getAccessRoles()
    {
        return ['ROLE_ADMIN'];
    }

    /**
     * @return array
     */
    public function collectData()
    {
        return [
            'data' => [
                'route' => '/',
                'inlineTransEnabled' => $this->request->query->get('inline-trans', false)
            ],
        ];

    }

    /**
     * @param Request         $request
     * @param Response        $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (!$this->showDataCollection($request, $response) || !$this->isEnabled()) {
            $this->data = false;
        } else {
            $this->data = $this->collectData();
        }
    }

    /**
     * Gets the data for template
     *
     * @return array The request events
     */
    public function getTemplateData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kuma_inline_translations';
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }
}
