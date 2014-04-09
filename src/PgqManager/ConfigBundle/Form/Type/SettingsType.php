<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\ConfigBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('databases', 'bootstrap_collection', array(
            'type'           => new DatabaseType(),
            'allow_add'      => true,
            'allow_delete'   => true,
            'label'          => false,
            'sub_widget_col' => 10,
            'button_col'     => 2,
            'required'       => true,
            'by_reference' => false
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PgqManager\ConfigBundle\Entity\Settings'
        ));
    }

    public function getName()
    {
        return 'settings';
    }
}