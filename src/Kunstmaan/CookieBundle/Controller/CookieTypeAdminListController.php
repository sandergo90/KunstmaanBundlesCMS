<?php

namespace Kunstmaan\CookieBundle\Controller;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\CookieBundle\AdminList\CookieTypeAdminListConfigurator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CookieTypeAdminListController
 *
 * @package Kunstmaan\CookieBundle\Controller
 */
class CookieTypeAdminListController extends AdminListController
{
    /* @var AdminListConfiguratorInterface */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        if (null === $this->configurator) {
            $this->configurator = new CookieTypeAdminListConfigurator($this->getEntityManager());
        }

        return $this->configurator;
    }

    /**
     * @Route("/", name="kunstmaancookiebundle_admin_cookietype")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * @Route("/add", name="kunstmaancookiebundle_admin_cookietype_add")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="kunstmaancookiebundle_admin_cookietype_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function editAction(Request $request, int $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/{id}/view", requirements={"id" = "\d+"}, name="kunstmaancookiebundle_admin_cookietype_view")
     * @Method({"GET"})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function viewAction(Request $request, int $id)
    {
        return parent::doViewAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="kunstmaancookiebundle_admin_cookietype_delete")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function deleteAction(Request $request, int $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv|ods|xlsx"}, name="kunstmaancookiebundle_admin_cookietype_export")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param string  $_format
     *
     * @return Response
     */
    public function exportAction(Request $request, string $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }

    /**
     * @Route("/{id}/move-up", requirements={"id" = "\d+"}, name="kunstmaancookiebundle_admin_cookietype_move_up")
     * @Method({"GET"})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse
     */
    public function moveUpAction(Request $request, int $id)
    {
        return parent::doMoveUpAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/{id}/move-down", requirements={"id" = "\d+"}, name="kunstmaancookiebundle_admin_cookietype_move_down")
     * @Method({"GET"})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse
     */
    public function moveDownAction(Request $request, int $id)
    {
        return parent::doMoveDownAction($this->getAdminListConfigurator(), $id, $request);
    }
}
