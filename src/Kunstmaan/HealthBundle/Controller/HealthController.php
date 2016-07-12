<?php

namespace Kunstmaan\HealthBundle\Controller;

use Kunstmaan\HealthBundle\Manager\WidgetManager;
use Kunstmaan\HealthBundle\Widget\HealthWidget;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HealthController
 * @package Kunstmaan\HealthBundle\Controller
 *
 * @Route(service="kunstmaan_health.controller.health")
 */
class HealthController
{

    /** @var WidgetManager */
    private $widgetManager;

    public function __construct(WidgetManager $widgetManager)
    {
        $this->widgetManager = $widgetManager;
    }

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="kunstmaan_health")
     * @Template("@KunstmaanHealth/index.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        /** @var HealthWidget[] $widgets */
        $widgets = $this->widgetManager->getWidgets();

        $id = $request->get('id');
        
        return ['widgets' => $widgets, 'id' => $id];
    }
}
