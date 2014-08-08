<?php
/**
 * Copyright (c) 2014 HiMedia Group
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2014 HiMedia Group
 * @author Karl MARQUES <kmarques@hi-media.com>
 * @license Apache License, Version 2.0
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