<?php

namespace Kunstmaan\CookieBundle\Controller;

use Kunstmaan\NodeBundle\Entity\Node;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LegalController
 *
 * @package Kunstmaan\CookieBundle\Controller
 */
class LegalController extends AbstractController
{
    /**
     * @Route("/modal/{internal_name}", name="kunstmaancookiebundle_legal_modal")
     * @ParamConverter("node", class="Kunstmaan\NodeBundle\Entity\Node", options={
     *    "repository_method" = "getNodeByInternalName",
     *    "mapping": {"internal_name": "internalName"},
     *    "map_method_signature" = true
     * })
     */
    public function switchTabAction(Request $request, Node $node)
    {
        $page = $node->getNodeTranslation($request->getLocale())->getRef($this->getDoctrine()->getManager());

        return $this->render(
            '@KunstmaanCookie/CookieBar/_modal.html.twig',
            [
                'node' => $node,
                'page' => $page,
            ]
        );
    }
}
