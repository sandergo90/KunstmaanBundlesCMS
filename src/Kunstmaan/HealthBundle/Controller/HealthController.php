<?php

namespace Kunstmaan\HealthBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\HealthBundle\Helper\UrlChecker\DeadLinkFinder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Cmf\Component\Routing\ChainRouter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Templating\EngineInterface;


/**
 * Class HealthController
 * @package Kunstmaan\HealthBundle\Controller
 *
 * @Route(service="kunstmaan_health.controller.health")
 */
class HealthController
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
     * Generates the health page
     *
     * @param Request $request
     * @return array|RedirectResponse
     *
     * @Template("@KunstmaanHealth/index.html.twig")
     */
    public function indexAction(Request $request)
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

        return ['pagerfanta' => $pagerfanta];
    }
}
