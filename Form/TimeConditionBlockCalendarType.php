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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TelNowEdge\FreePBX\Base\Form\RepositoryType;
use TelNowEdge\Module\tnetc\Helper\CalendarHelper;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlockCalendar;

class TimeConditionBlockCalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('policy', ChoiceType::class, array(
                'choices' => array(
                    'Match on «Busy state»' => 'straight',
                    'Match on «Free state»' => 'inverse',
                ),
            ))
            ->add('calendar', RepositoryType::class, array(
                'repository' => CalendarHelper::class,
                'caller' => 'getByType',
                'parameters' => array('local'),
                'choice_label' => function ($x) {
                    return $x->getName();
                },
                'choice_value' => function ($x) {
                    return base64_encode(json_encode(
                        array(
                            'id' => $x->getId(),
                            'type' => $x->getType(),
                        )
                    ));
                },
                'attr' => array(
                    'data-link' => 'config.php?display=calendar&action=view&type=__type__&id=__id__',
                ),
                'label' => false,
            ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => TimeConditionBlockCalendar::class,
            ));
    }
}
