<?php

namespace Kunstmaan\HealthBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ConfigMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children
     *
     * @param MenuBuilder $menu The MenuBuilder
     * @param MenuItem[] &$children The current children
     * @param MenuItem $parent The parent Menu item
     * @param Request $request The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (!is_null($parent) && 'KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            $hasAccess = false;
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $hasAccess = true;
            }

            if ($hasAccess) {
                $menuItem = new MenuItem($menu);
                $menuItem
                    ->setRoute('kunstmaan_health')
                    ->setLabel('settings.health.title')
                    ->setUniqueId('Health')
                    ->setParent($parent);

                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }
        }
    }
}
