<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\ConfigBundle\Form\Type;


use PgqManager\ConfigBundle\Entity\Database;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatabaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('displayName', 'text',
            array('label' => 'Display Name',
                  'required' => true,
                  'attr'  => array(
                      'class' => 'col-md-3'
                  )
            )
        );

        $builder->add('driver', 'choice',
            array(
                'label'   => 'Driver',
                'required' => true,
                'choices' => Database::getDrivers(),
                'attr'    => array(
                    'class' => 'col-md-3'
                )
            )
        );

        $builder->add('name', 'text',
            array('label' => 'Database name',
                  'required' => true,
                  'attr'  => array(
                      'class' => 'col-md-3'
                  )
            )
        );

        $builder->add('host', 'text',
            array('label' => 'Hostname',
                  'required' => true,
                  'attr'  => array(
                      'class' => 'col-md-3'
                  )
            )
        );

        $builder->add('port', 'text',
            array('label' => 'Port',
                  'required' => true,
                  'attr'  => array(
                      'class' => 'col-md-3'
                  )
            )
        );

        $builder->add('user', 'text',
            array('label' => 'User',
                  'required' => true,
                  'attr'  => array(
                      'class' => 'col-md-3'
                  )
            )
        );

        $builder->add('password', 'text',
            array('label' => 'Password',
                  'attr'  => array(
                      'class' => 'col-md-3'
                  )
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PgqManager\ConfigBundle\Entity\Database',
            'attr'       => array(
                'class' => 'thumbnail row'
            )
        ));
    }

    public function getName()
    {
        return 'database';
    }
}