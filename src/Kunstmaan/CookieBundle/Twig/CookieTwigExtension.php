<?php

namespace Kunstmaan\CookieBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\CookieBundle\Entity\CookieType;

/**
 * Class CookieTwigExtension
 *
 * @package Kunstmaan\CookieBundle\Twig
 */
class CookieTwigExtension extends \Twig_Extension
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * CookieTwigExtension constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
        ];
    }

    /**
     * @return array|CookieType[]
     */
    public function getCookieTypes()
    {
        return $this->em->getRepository('KunstmaanCookieBundle:CookieType')->findAll();
    }
}
