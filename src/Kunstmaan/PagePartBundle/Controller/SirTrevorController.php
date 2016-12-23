<?php

namespace Kunstmaan\PagePartBundle\Controller;

use Doctrine\Common\Util\ClassUtils;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the sir trevor
 */
class SirTrevorController extends Controller
{
    /**
     * @Route("/sir-trevor", name="KunstmaanPagePartBundle_sir_trevor")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return JsonResponse
     */
    public function sirTrevorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($request->get('nodeTranslationId'));
        $pagePartRef = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->findOneBy([
            'pagePartId' => $request->get('pagePartId'),
            'pagePartEntityname' => $request->get('class')
        ]);
        $pagePart = $em->getRepository($pagePartRef->getPagePartEntityName())->find($pagePartRef->getPagePartId());

        $field = $request->get('field');
        $func = "set".ucwords($field);

        $nodeVersion = $this->get('kunstmaan_node.admin_node.publisher')
            ->createPublicVersion(
                $nodeTranslation->getRef($em),
                $nodeTranslation,
                $nodeTranslation->getPublicNodeVersion(),
                $this->getUser()
            );

        $pagePart->$func($request->get('value'));
        $em->persist($pagePart);

        $nodeVersion->setUpdated(new \DateTime());
        if ($nodeVersion->getType() == 'public') {
            $nodeTranslation->setUpdated($nodeVersion->getUpdated());
        }

        $em->persist($nodeVersion);
        $em->persist($nodeTranslation);

        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
