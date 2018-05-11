<?php

namespace Kunstmaan\TranslatorBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Translation\Bundle\EditInPlace\ActivatorInterface;

/**
 * Class EditInPlaceResponseListener
 */
final class InlineTranslationResponseListener implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        if (HttpKernel::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $content = $event->getResponse()->getContent();

        // Clean the content for malformed tags in attributes or encoded tags
        $replacement = "\"$1🚫 Can't be translated here. 🚫\"";
        $pattern = "@\\s*[\"']\\s*(.[a-zA-Z]+:|)(<inline-trans.+data-keyword=\"([^&\"]+)\".+?(?=<\\/inline-trans)<\\/inline-trans>)\\s*[\"']@mi";
        //        if (!$this->showUntranslatable) {
        //            $replacement = '"$3"';
        //        }
//        $content = preg_replace($pattern, $replacement, $content);

        // Remove escaped content (e.g. Javascript)
        $pattern = '@&lt;inline-trans.+data-keyword=&quot;([^&]+)&quot;.+&lt;\\/inline-trans&gt;@mi';
        $replacement = '🚫 $1 🚫';
        //        if (!$this->showUntranslatable) {
        //            $replacement = '$2';
        //        }
//        $content = preg_replace($pattern, $replacement, $content);

        $response = $event->getResponse();

        // Remove the cache because we do not want the modified page to be cached
        $response->headers->set('cache-control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('pragma', 'no-cache');
        $response->headers->set('expires', '0');

        $event->getResponse()->setContent($content);
    }
}
