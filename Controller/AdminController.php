<?php
/**
 * @package Newscoop\ArticleRecommendationBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticleRecommendationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Newscoop\ArticleRecommendationBundle\Form\Type\SettingsType;

class AdminController extends Controller
{
    /**
    * @Route("/admin/article-recommendation")
    * @Template()
    */
    public function indexAction(Request $request)
    {   
        $em = $this->container->get('em');
        $translator = $this->container->get('translator');
        
        $settings = $em->getRepository('Newscoop\ArticleRecommendationBundle\Entity\Settings')->findOneBy(array(
            'is_active' => true
        ));

        $form = $this->container->get('form.factory')->create(new SettingsType(), array(
            'captcha' => $settings->getCaptcha(),
            'forLoggedIn' => $settings->getForLoggedIn()
        ), array());

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $settings->setCaptcha($data['captcha']);
                $settings->setForLoggedIn($data['forLoggedIn']);
                $settings->setCreatedAt(new \Datetime("now"));
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', $translator->trans('plugin.recommendation.msg.saved'));

                return $this->redirect($this->generateUrl('newscoop_articlerecommendation_admin_index'));
            } else {
                $this->get('session')->getFlashBag()->add('error', $translator->trans('plugin.recommendation.msg.error'));

                return $this->redirect($this->generateUrl('newscoop_articlerecommendation_admin_index'));
            }
        }

        return array(
            'form' => $form->createView(),
            'message' => $settings->getMessage()
        );
    }

    /**
    * @Route("/admin/article-recommendation/save-message")
    */
    public function saveMessageAction(Request $request)
    {    
        try {
            $em = $this->container->get('em');
            $settings = $em->getRepository('Newscoop\ArticleRecommendationBundle\Entity\Settings')->findOneBy(array(
                'is_active' => true
            ));

            $settings->setMessage($request->get('message'));
            $em->flush();
        } catch (\Exception $e) {
            return new Response(json_encode(array('status' => false)));
        }

        return new Response(json_encode(array('status' => true)));
    }
}