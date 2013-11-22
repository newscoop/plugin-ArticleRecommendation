<?php
/**
 * @package Newscoop\ArticleRecommendationBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticleRecommendationBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Article recommendation settings entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="plugin_article_recommendation_settings")
 */
class Settings 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", name="captcha")
     * @var bool
     */
    private $captcha;

    /**
     * @ORM\Column(type="boolean", name="for_logged")
     * @var bool
     */
    private $forLoggedIn;

    /**
     * @ORM\Column(type="text", name="message", nullable=true)
     * @var text
     */
    private $message;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @var datetime
     */
    private $created_at;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     * @var boolean
     */
    private $is_active;

    public function __construct() {
        $this->setCreatedAt(new \DateTime());
        $this->setIsActive(true);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get captcha
     *
     * @return bool
     */
    public function getCaptcha()
    {
        return $this->captcha;
    }

    /**
     * Set captcha
     *
     * @param  bool $captcha
     * @return bool
     */
    public function setCaptcha($captcha)
    {
        $this->captcha = $captcha;
        
        return $captcha;
    }

    /**
     * Get forLoggedIn
     *
     * @return bool
     */
    public function getForLoggedIn()
    {
        return $this->forLoggedIn;
    }

    /**
     * Set forLoggedIn
     *
     * @param  bool $forLoggedIn
     * @return bool
     */
    public function setForLoggedIn($forLoggedIn)
    {
        $this->forLoggedIn = $forLoggedIn;
        
        return $this;
    }

    /**
     * Get message
     *
     * @return text
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param  text $message
     * @return text
     */
    public function setMessage($message)
    {
        $this->message = $message;
        
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set status
     *
     * @param boolean $is_active
     * @return boolean
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;
        
        return $this;
    }

    /**
     * Get create date
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set create date
     *
     * @param datetime $created_at
     * @return datetime
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at;
        
        return $this;
    }
}