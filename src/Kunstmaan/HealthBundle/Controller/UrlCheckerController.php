<?php

namespace Kunstmaan\HealthBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\HealthBundle\Helper\UrlChecker\DeadLinkFinder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Cmf\Component\Routing\ChainRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class UrlCheckerController
 * @package Kunstmaan\HealthBundle\Controller
 *
 * @Route(service="kunstmaan_health.controller.url_checker")
 */
class UrlCheckerController
{
    /**
     * @var ChainRouter
     */
    private $router;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var DeadLinkFinder $deadLinkFinder
     */
    private $deadLinkFinder;

    /**
     * @param ChainRouter $router
     * @param EngineInterface $templating
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param EntityManagerInterface $em
     */
    public function __construct(ChainRouter $router, EngineInterface $templating, AuthorizationCheckerInterface $authorizationChecker, EntityManagerInterface $em, DeadLinkFinder $deadLinkFinder)
    {
        $this->router = $router;
        $this->templating = $templating;
        $this->authorizationChecker = $authorizationChecker;
        $this->em = $em;
        $this->deadLinkFinder = $deadLinkFinder;
    }

    /**
     * The index action will render the url checker widget
     *
     * @Route("/", name="KunstmaanHealthBundle_widget_urlchecker")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function widgetAction(Request $request)
    {
        if (false === $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $links = $this->deadLinkFinder->run();

        $adapter = new ArrayAdapter($links);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(20);

        $pagenumber = $request->get('page');
        if (!$pagenumber || $pagenumber < 1) {
            $pagenumber = 1;
        }
        $pagerfanta->setCurrentPage($pagenumber);

        return [
            'id' => 'urlchecker',
            'title' => 'Url Checker',
            'pagerfanta' => $pagerfanta
        ];
    }
}
