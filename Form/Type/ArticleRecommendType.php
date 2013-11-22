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

class ArticleRecommendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
        ))
        ->add('recaptcha', 'newscoop_recaptcha', array(
            'attr' => array(
                'options' => array(
                    'theme' => 'white'
                )
            ),
            'mapped' => false,
            'constraints'   => array(
                new True()
            )
        ));
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