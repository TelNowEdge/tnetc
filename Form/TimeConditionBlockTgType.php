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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TelNowEdge\FreePBX\Base\Form\RepositoryType;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlockTg;
use TelNowEdge\Module\tnetc\Repository\TimeGroupRepository;

class TimeConditionBlockTgType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timeGroup', RepositoryType::class, array(
                'repository' => TimeGroupRepository::class,
                'caller' => 'getCollection',
                'choice_label' => function ($x) {
                    return $x->getDescription();
                },
                'choice_value' => function ($x) {
                    return base64_encode(json_encode(
                        array('id' => $x->getId())
                    ));
                },
                'attr' => array(
                    'data-link' => 'config.php?display=timegroups&view=form&extdisplay=__id__',
                ),
                'label' => false,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => TimeConditionBlockTg::class,
            ));
    }
}
