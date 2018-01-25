<?php

namespace Kunstmaan\CookieBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class CookieMenuController
 *
 * @package Kunstmaan\CookieBundle\Controller
 */
class CookieMenuController extends AbstractController
{
    /**
     * @Route("/", name="kunstmaancookiebundle_admin_cookies")
     *
     * @return RedirectResponse
     */
    public function cookiesAction()
    {
        return $this->redirectToRoute('kunstmaancookiebundle_admin_cookietype');
    }
}
