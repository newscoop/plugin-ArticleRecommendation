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

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('captcha', 'checkbox', array(
            'label' => 'plugin.recommendation.label.captcha',
            'required' => false
        ))
        ->add('forLoggedIn', 'checkbox', array(
            'label' => 'plugin.recommendation.label.forlogged',
            'required' => false
        ));
    }

    public function getName()
    {
        return 'settingsform';
    }
}