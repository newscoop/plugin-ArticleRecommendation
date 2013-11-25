<?php
/**
 * @package Newscoop\ArticleRecommendationBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticleRecommendationBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Newscoop\EventDispatcher\Events\GenericEvent;
use Newscoop\ArticleRecommendationBundle\Entity\Settings;

/**
 * Event lifecycle management
 */
class LifecycleSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function install(GenericEvent $event)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->updateSchema($this->getClasses(), true);

        // Generate proxies for entities
        $this->em->getProxyFactory()->generateProxyClasses($this->getClasses(), __DIR__ . '/../../../../library/Proxy');

        $settings = new Settings();
        $settings->setCaptcha(true);
        $settings->setForLoggedIn(true);
        $settings->setMessage(
        'Description: %articleLead%

        Read: %articleLink%
        This e-mail was sent by %userName% via your website.');
        $settings->setIsActive(true);
        $settings->setCreatedAt(new \DateTime('now'));
        $this->em->persist($settings);
        $this->em->flush();
    }

    public function update(GenericEvent $event)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->updateSchema($this->getClasses(), true);

        // Generate proxies for entities
        $this->em->getProxyFactory()->generateProxyClasses($this->getClasses(), __DIR__ . '/../../../../library/Proxy');
    }

    public function remove(GenericEvent $event)
    {   
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->dropSchema($this->getClasses(), true);
    }

    public static function getSubscribedEvents()
    {
        return array(
            'plugin.install.newscoop_article_recommendation_plugin' => array('install', 1),
            'plugin.update.newscoop_article_recommendation_plugin' => array('update', 1),
            'plugin.remove.newscoop_article_recommendation_plugin' => array('remove', 1),
        );
    }

    private function getClasses(){
        return array(
            $this->em->getClassMetadata('Newscoop\ArticleRecommendationBundle\Entity\Settings')
        );
    }
}
