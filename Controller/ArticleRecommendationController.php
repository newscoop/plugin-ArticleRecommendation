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
use Newscoop\ArticleRecommendationBundle\Form\Type\ArticleRecommendType;

class ArticleRecommendationController extends Controller
{
    /**
    * @Route("/plugin/article-recommendation/send")
    * @Template()
    */
    public function sendAction(Request $request)
    {   
        $em = $this->container->get('em');
        $translator = $this->container->get('translator');
        $mailer = $this->container->get('mailer');
        $user = $this->container->get('user')->getCurrentUser();
        $form = $this->container->get('form.factory')->create(new ArticleRecommendType(), array(), array());
        $isLoggedIn = false;
        $settings = $em->getRepository('Newscoop\ArticleRecommendationBundle\Entity\Settings')->findOneBy(array(
            'is_active' => true
        ));

        $loggedSetting = $settings->getForLoggedIn();

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($user) {
                $isLoggedIn = true;
            }

            if ($isLoggedIn && ($loggedSetting || !$loggedSetting) || !$isLoggedIn && !$loggedSetting) {
                if ($form->isValid()) {
                    $data = $form->getData();
            
                    if ($user) {
                        $data['sender_email'] = $user->getEmail();
                        $data['sender_name'] = $user->getRealName();
                    }

                    $article = $em->getRepository('Newscoop\Entity\Article')->findOneBy(array(
                        'number' => $data['article_number']
                    ));

                    $link = \ShortURL::GetURL(
                        $article->getPublicationId(),
                        $article->getLanguageId(),
                        $article->getIssueId(),
                        $article->getSectionId(),
                        $article->getNumber()
                    );

                    $message = \Swift_Message::newInstance()
                        ->setSubject($translator->trans('plugin.recommendation.email.subject', array('%article%' => $article->getName())))
                        ->setFrom($data['sender_email'])
                        ->setTo($data['recipient_email'])
                        ->setBody(
                            $this->renderView(
                                'NewscoopArticleRecommendationBundle::email.txt.twig',
                                array(
                                    'usermessage' => $data['message'],
                                    'message' => $translator->trans($settings->getMessage(), array(
                                        '%articleLead%' => $article->getData('deck') ? $article->getData('deck') : $article->getName(),
                                        '%link%' => $link,
                                        '%user%' => $data['sender_name']
                                    ))
                                )
                            )
                        );

                    $mailer->send($message);

                    return new Response(json_encode(array('status' => true)));
                }
            } else {
                return new Response(json_encode(array('status' => false)));
            }
        }
    }
}