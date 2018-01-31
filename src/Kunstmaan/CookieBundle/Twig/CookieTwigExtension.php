<?php

namespace Kunstmaan\CookieBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\CookieBundle\Entity\CookieType;
use Kunstmaan\CookieBundle\Helper\LegalCookieHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CookieTwigExtension
 *
 * @package Kunstmaan\CookieBundle\Twig
 */
class CookieTwigExtension extends \Twig_Extension
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var LegalCookieHelper */
    private $cookieHelper;

    /**
     * CookieTwigExtension constructor.
     *
     * @param EntityManagerInterface $em
     * @param LegalCookieHelper      $cookieHelper
     */
    public function __construct(EntityManagerInterface $em, LegalCookieHelper $cookieHelper)
    {
        $this->em = $em;
        $this->cookieHelper = $cookieHelper;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_cookie_types', [$this, 'getCookieTypes']),
            new \Twig_SimpleFunction('get_legal_cookie', [$this, 'getLegalCookie']),
        ];
    }

    /**
     * @return array|CookieType[]
     */
    public function getCookieTypes()
    {
        return $this->em->getRepository('KunstmaanCookieBundle:CookieType')->findAll();
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     */
    public function getLegalCookie(Request $request)
    {
        return $this->cookieHelper->getLegalCookie($request);
    }
}
