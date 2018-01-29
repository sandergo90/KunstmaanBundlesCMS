<?php

namespace Kunstmaan\CookieBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\CookieBundle\Entity\CookieLog;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LegalCookieHelper
 */
class LegalCookieHelper
{
    const LEGAL_COOKIE_NAME = 'legal_cookie';

    const FUNCTIONAL_COOKIE_NAME = 'functional_cookie';

    /** @var array */
    private $legalCookie;

    /** @var EntityManagerInterface */
    private $em;

    /**
     * LegalCookieHelper constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     */
    public function findOrCreateLegalCookie(Request $request)
    {
        if (null === $this->legalCookie) {
            $cookies = [];
            if (!$request->cookies->has(self::LEGAL_COOKIE_NAME)) {
                $types = $this->em->getRepository('KunstmaanCookieBundle:CookieType')->findAll();
                foreach ($types as $type) {
                    if ($type->isAlwaysOn()) {
                        $cookies[$type->getInternalName()] = true;
                    } else {
                        $cookies[$type->getInternalName()] = false;
                    }
                }
            }
            $this->legalCookie = $request->cookies->get(self::LEGAL_COOKIE_NAME, serialize($cookies));
        }

        return unserialize($this->legalCookie, [false]);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function getLegalCookie(Request $request)
    {
        if (null === $this->legalCookie) {
            $this->legalCookie = $request->cookies->get(self::LEGAL_COOKIE_NAME);
        }

        return unserialize($this->legalCookie, [false]);
    }

    /**
     * @param Request $request
     * @param array   $legalCookie
     *
     * @return Cookie
     */
    public function saveLegalCookie(Request $request, array $legalCookie)
    {
        $log = new CookieLog();
        $log->setIpAddress($request->getClientIp());
        $log->setCreated(new \DateTime('now'));

        $this->em->persist($log);
        $this->em->flush();

        $legalCookie['cookie_log_id'] = $log->getId();

        return new Cookie(self::LEGAL_COOKIE_NAME, serialize($legalCookie));
    }
}
