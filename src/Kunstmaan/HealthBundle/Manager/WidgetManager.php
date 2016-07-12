<?php

namespace Kunstmaan\HealthBundle\Manager;


use Kunstmaan\HealthBundle\Widget\HealthWidget;

class WidgetManager
{

    /**
     * @var HealthWidget[]
     */
    private $widgets = array();

    /**
     * @param HealthWidget $widget
     */
    public function addWidget(HealthWidget $widget)
    {
        $this->widgets[] = $widget;
    }

    /**
     * @return HealthWidget[]
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

}
