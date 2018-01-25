<?php

namespace Kunstmaan\CookieBundle\EventSubscriber;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CookieBarEventSubscriber
 *
 * @package Kunstmaan\CookieBundle\EventSubscriber
 */
class CookieBarEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var AdminRouteHelper
     */
    protected $adminRouteHelper;

    /**
     * CookieBarListener constructor.
     *
     * @param \Twig_Environment     $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param AdminRouteHelper      $adminRouteHelper
     */
    public function __construct(\Twig_Environment $twig, UrlGeneratorInterface $urlGenerator, AdminRouteHelper $adminRouteHelper)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->adminRouteHelper = $adminRouteHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -125],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        $url = $event->getRequest()->getRequestUri();

        // Do not capture redirects or modify XML HTTP Requests
        if (!$event->isMasterRequest() || $request->isXmlHttpRequest() || $this->adminRouteHelper->isAdminRoute($url)) {
            return;
        }

        if ($response->isRedirection() || ($response->headers->has('Content-Type') && false === strpos(
                    $response->headers->get('Content-Type'),
                    'html'
                ))
            || 'html' !== $request->getRequestFormat()
            || false !== stripos($response->headers->get('Content-Disposition'), 'attachment;')
        ) {
            return;
        }

        $this->injectCookieBar($response);
    }

    /**
     * @param Response $response
     */
    protected function injectCookieBar(Response $response)
    {
        $content = $response->getContent();
        $pos = strripos($content, '</kuma-cookie-bar>');

        if (false !== $pos) {
            $toolbar = "\n".str_replace(
                    "\n",
                    '',
                    $this->twig->render('@KunstmaanCookie/CookieBar/view.html.twig')
                )."\n";
            $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
            $response->setContent($content);
        }
    }
}
