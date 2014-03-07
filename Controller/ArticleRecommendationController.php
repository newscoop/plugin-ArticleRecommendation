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
use Symfony\Component\HttpFoundation\JsonResponse;
use Newscoop\ArticleRecommendationBundle\Form\Type\ArticleRecommendType;

class ArticleRecommendationController extends Controller
{
    /**
    * @Route("/plugin/article-recommendation/send")
    */
    public function sendAction(Request $request)
    {
        $em = $this->container->get('em');
        $translator = $this->container->get('translator');
        $mailer = $this->container->get('mailer');
        $user = $this->container->get('user')->getCurrentUser();
        $form = $this->container->get('form.factory')->create(new ArticleRecommendType($em), array(), array());
        $isLoggedIn = false;
        $response = array();
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

                    $link = $this->get('article.link')->getLinkCanonical($article);

                    try {
                        if ($article->getType() === $data['article_type']) {
                            $articleLead = $data['custom_field_type'] ? strip_tags($article->getData($data['custom_field_type'])) : $article->getName();
                        } else {
                            try {
                                $articleLead = $data['field_type'] ? strip_tags($article->getData($data['field_type'])) : $article->getName();
                            } catch (\Exception $e) {
                                $articleLead = $article->getName();
                            }
                        }
                    } catch (\Exception $e) {
                        return new JsonResponse(array(
                            'status' => false,
                            'response' => $e->getMessage()
                        ));
                    }

                    try {
                        $message = \Swift_Message::newInstance()
                            ->setSubject($translator->trans('plugin.recommendation.email.subject', array('%articleName%' => $article->getName())))
                            ->setFrom($data['sender_email'])
                            ->setTo($data['recipient_email'])
                            ->setBody(
                                $this->renderView(
                                    'NewscoopArticleRecommendationBundle::email.txt.twig',
                                    array(
                                        'usermessage' => $data['message'],
                                        'message' => $translator->trans($settings->getMessage(), array(
                                            '%articleLead%' => $articleLead,
                                            '%articleLink%' => $link,
                                            '%userName%' => $data['sender_name']
                                        ))
                                    )
                                )
                            );

                        $mailer->send($message);

                        $response['response'] = array('status' => true);
                    } catch (\Exception $e) {
                        $response['response'] = array(
                            'status' => false,
                            'message' => $e->getMessage()
                        );
                    }
                } else {
                    $response['response'] = array(
                        'status' => false,
                        'message' => $translator->trans('plugin.recommendation.msg.invalid')
                    );
                }
            } else {
                $response['response'] = array(
                    'status' => false,
                    'message' => $translator->trans('plugin.recommendation.msg.loggedin')
                );
            }

            return new JsonResponse($response);
        }
    }
}