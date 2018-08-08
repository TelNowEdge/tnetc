<?php

/*
 * Copyright [2018] [TelNowEdge]
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
 */

namespace TelNowEdge\Module\tnetc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TelNowEdge\FreePBX\Base\Form\DestinationType;
use TelNowEdge\FreePBX\Base\Form\RepositoryType;
use TelNowEdge\Module\tnetc\Model\TimeCondition;
use TelNowEdge\Module\tnetc\Repository\DayNightRepository;

class TimeConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('internalDial')
            ->add('dayNight', RepositoryType::class, array(
                'repository' => DayNightRepository::class,
                'caller' => 'getCollection',
                'choice_label' => function ($x) {
                    return sprintf('[%d] %s', $x->getExt(), $x->getFcDescription());
                },
                'choice_value' => function ($x) {
                    return $x->getExt();
                },
                'placeholder' => '-',
                'required' => false,
            ))
            ->add('timezone', TimezoneType::class, array(
                'preferred_choices' => array('Europe/Paris'),
            ))
            ->add('timeConditionBlocks', CollectionType::class, array(
                'label' => 'If blocks',
                'entry_type' => TimeConditionBlockType::class,
                'entry_options' => array(
                    'label' => false,
                    'attr' => array(
                        'data-child' => true,
                    ),
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => true,
            ))
            ->add('fallback', DestinationType::class, array(
                'required' => true,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => TimeCondition::class,
            ));
    }
}
