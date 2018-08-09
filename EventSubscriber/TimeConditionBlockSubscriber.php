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
use TelNowEdge\Module\tnetc\Event\TimeConditionBlockEvent;
use TelNowEdge\Module\tnetc\Handler\Dbhandler\TimeConditionBlockCalendarDbHandler;
use TelNowEdge\Module\tnetc\Handler\Dbhandler\TimeConditionBlockHintDbHandler;
use TelNowEdge\Module\tnetc\Handler\Dbhandler\TimeConditionBlockTgDbHandler;

class TimeConditionBlockSubscriber implements EventSubscriberInterface, ContainerAwareInterface
{
    /**
     * Symfony\Component\DependencyInjection\ContainerInterface.
     */
    private $container;

    public static function getSubscribedEvents()
    {
        return array(
            TimeConditionBlockEvent::CREATE_POST_SAVE => array(
                array('createTimeGroup', 1000),
                array('createCalendar', 1000),
                array('createHint', 1000),
            ),
        );
    }

    public function createTimeGroup(TimeConditionBlockEvent $event)
    {
        $timeConditionBlock = $event->getTimeConditionBlock();

        foreach ($timeConditionBlock->getTimeConditionBlockTgs() as $timeGroup) {
            $timeGroup->setTimeConditionBlock($timeConditionBlock);

            $this->container
                ->get(TimeConditionBlockTgDbHandler::class)
                ->create($timeGroup)
                ;
        }
    }

    public function createCalendar(TimeConditionBlockEvent $event)
    {
        $timeConditionBlock = $event->getTimeConditionBlock();

        foreach ($timeConditionBlock->getTimeConditionBlockCalendars() as $timeGroup) {
            $timeGroup->setTimeConditionBlock($timeConditionBlock);

            $this->container
                ->get(TimeConditionBlockCalendarDbHandler::class)
                ->create($timeGroup)
                ;
        }
    }

    public function createHint(TimeConditionBlockEvent $event)
    {
        $timeConditionBlock = $event->getTimeConditionBlock();

        foreach ($timeConditionBlock->getTimeConditionBlockHints() as $timeGroup) {
            $timeGroup->setTimeConditionBlock($timeConditionBlock);

            $this->container
                ->get(TimeConditionBlockHintDbHandler::class)
                ->create($timeGroup)
                ;
        }
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
