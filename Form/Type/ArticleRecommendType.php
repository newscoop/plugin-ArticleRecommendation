<?php
/**
 * @package Newscoop\ArticleRecommendationBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticleRecommendationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\True;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class ArticleRecommendType extends AbstractType
{   
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $settings =  $this->em->getRepository('Newscoop\ArticleRecommendationBundle\Entity\Settings')->findOneBy(array(
            'is_active' => true
        ));

        $builder->add('recipient_email', 'email', array(
            'label' => 'plugin.recommendation.label.recipientemail',
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('sender_email', 'email', array(
            'label' => 'plugin.recommendation.label.senderemail',
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('sender_name', 'text', array(
            'label' => 'plugin.recommendation.label.senderemail',
            'error_bubbling' => true,
            'max_length' => 50,
            'required' => false
        ))
        ->add('message', 'textarea', array(
            'label' => 'plugin.recommendation.label.message',
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('article_number', 'hidden', array(
            'required' => true
        ));

        if ($settings->getCaptcha()) {
            $builder->add('recaptcha', 'ewz_recaptcha', array(
                'mapped' => false,
                'constraints'   => array(
                    new True()
                )
            ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'articlerecommendform';
    }
}