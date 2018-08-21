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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TelNowEdge\FreePBX\Base\Form\DestinationType;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlock;

class TimeConditionBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timeConditionBlockTgs', CollectionType::class, array(
                'label' => 'Time group',
                'entry_type' => TimeConditionBlockTgType::class,
                'entry_options' => array(
                    'label' => false,
                    'attr' => array(
                        'data-child-tg' => true,
                    ),
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__child__',
                'by_reference' => false,
            ))
            ->add('timeConditionBlockCalendars', CollectionType::class, array(
                'label' => 'Calendar',
                'entry_type' => TimeConditionBlockCalendarType::class,
                'entry_options' => array(
                    'label' => false,
                    'attr' => array(
                        'data-child-calendar' => true,
                    ),
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__child__',
                'by_reference' => false,
            ))
            ->add('timeConditionBlockHints', CollectionType::class, array(
                'label' => 'BLF',
                'entry_type' => TimeConditionBlockHintType::class,
                'entry_options' => array(
                    'label' => false,
                    'attr' => array(
                        'data-child-hint' => true,
                    ),
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__child__',
                'by_reference' => false,
            ))
            ->add('goto', DestinationType::class, array(
                'required' => true,
            ))
            ->add('weight', HiddenType::class)
            ->add('isActive', HiddenType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => TimeConditionBlock::class,
            ));
    }
}
