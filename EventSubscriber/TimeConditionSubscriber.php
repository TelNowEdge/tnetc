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

namespace TelNowEdge\Module\tnetc\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TelNowEdge\Module\tnetc\Event\TimeConditionEvent;
use TelNowEdge\Module\tnetc\Handler\DbHandler\TimeConditionBlockDbHandler;
use TelNowEdge\Module\tnetc\Helper\CollectionHelper;

class TimeConditionSubscriber implements EventSubscriberInterface, ContainerAwareInterface
{
    /**
     * Symfony\Component\DependencyInjection\ContainerInterface.
     */
    private $container;

    public static function getSubscribedEvents()
    {
        return array(
            TimeConditionEvent::CREATE_POST_SAVE => array(
                array('createBlock', 1000),
            ),
            TimeConditionEvent::UPDATE_POST_SAVE => array(
                array('updateBlock', 1000),
            ),
            TimeConditionEvent::DELETE_PRE_SAVE => array(
                array('deleteBlock', 1000),
            ),
        );
    }

    public function createBlock(TimeConditionEvent $event)
    {
        $timeCondition = $event->getTimeCondition();

        foreach ($timeCondition->getTimeConditionBlocks() as $block) {
            $block->setTimeCondition($timeCondition);

            $this->container
                ->get(TimeConditionBlockDbHandler::class)
                ->create($block)
                ;
        }
    }

    public function updateBlock(TimeConditionEvent $event)
    {
        $timeCondition = $event->getTimeCondition();
        $collectionHelper = CollectionHelper::create();
        $blocks = $collectionHelper->getByName('block');
        $updated = $collectionHelper->compileUpdated(
            'block',
            $timeCondition->getTimeConditionBlocks()
        );

        foreach ($updated as $block) {
            $this->container
                ->get(TimeConditionBlockDbHandler::class)
                ->update($block)
                ;
        }

        foreach ($blocks->get('added') as $block) {
            if (null === $block->getWeight()) {
                continue; // Object new create a empty useless object
            }

            $block->setTimeCondition($timeCondition);

            $this->container
                ->get(TimeConditionBlockDbHandler::class)
                ->create($block)
                ;
        }

        foreach ($blocks->get('removed') as $block) {
            $this->container
                ->get(TimeConditionBlockDbHandler::class)
                ->delete($block)
                ;
        }
    }

    public function deleteBlock(TimeConditionEvent $event)
    {
        $timeCondition = $event->getTimeCondition();

        foreach ($timeCondition->getTimeConditionBlocks() as $block) {
            $block->setTimeCondition($timeCondition);

            $this->container
                ->get(TimeConditionBlockDbHandler::class)
                ->delete($block)
                ;
        }
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
