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
use TelNowEdge\Module\tnetc\Helper\CollectionHelper;

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
            TimeConditionBlockEvent::UPDATE_POST_SAVE => array(
                array('updateTimeGroup', 1000),
                array('updateCalendar', 1000),
                array('updateHint', 1000),
            ),
            TimeConditionBlockEvent::DELETE_PRE_SAVE => array(
                array('deleteTimeGroup', 1000),
                array('deleteCalendar', 1000),
                array('deleteHint', 1000),
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

    public function updateTimeGroup(TimeConditionBlockEvent $event)
    {
        $timeConditionBlock = $event->getTimeConditionBlock();
        $collectionHelper = CollectionHelper::create();
        $name = sprintf('tg%d', $timeConditionBlock->getId());

        try {
            $tgs = $collectionHelper
                ->getByName($name)
                ;
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $updated = $collectionHelper->compileUpdated(
            $name,
            $timeConditionBlock->getTimeConditionBlockTgs()
        );

        foreach ($updated as $tg) {
            $this->container
                ->get(TimeConditionBlockTgDbHandler::class)
                ->update($tg)
                ;
        }

        foreach ($tgs->get('added') as $tg) {
            $this->container
                ->get(TimeConditionBlockTgDbHandler::class)
                ->create($tg)
                ;
        }

        foreach ($tgs->get('removed') as $tg) {
            $this->container
                ->get(TimeConditionBlockTgDbHandler::class)
                ->delete($tg)
                ;
        }
    }

    public function updateCalendar(TimeConditionBlockEvent $event)
    {
        $timeConditionBlock = $event->getTimeConditionBlock();
        $collectionHelper = CollectionHelper::create();
        $name = sprintf('calendar%d', $timeConditionBlock->getId());

        try {
            $calendars = $collectionHelper->getByName($name);
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $updated = $collectionHelper->compileUpdated(
            $name,
            $timeConditionBlock->getTimeConditionBlockCalendars()
        );

        foreach ($updated as $calendar) {
            $this->container
                ->get(TimeConditionBlockCalendarDbHandler::class)
                ->update($calendar)
                ;
        }

        foreach ($calendars->get('added') as $calendar) {
            $this->container
                ->get(TimeConditionBlockCalendarDbHandler::class)
                ->create($calendar)
                ;
        }

        foreach ($calendars->get('removed') as $calendar) {
            $this->container
                ->get(TimeConditionBlockCalendarDbHandler::class)
                ->delete($calendar)
                ;
        }
    }

    public function updateHint(TimeConditionBlockEvent $event)
    {
        $timeConditionBlock = $event->getTimeConditionBlock();
        $collectionHelper = CollectionHelper::create();
        $name = sprintf('hint%d', $timeConditionBlock->getId());

        try {
            $hints = $collectionHelper->getByName($name)
                ;
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $updated = $collectionHelper->compileUpdated(
            $name,
            $timeConditionBlock->getTimeConditionBlockHints()
        );

        foreach ($updated as $hint) {
            $this->container
                ->get(TimeConditionBlockHintDbHandler::class)
                ->update($hint)
                ;
        }

        foreach ($hints->get('added') as $hint) {
            $this->container
                ->get(TimeConditionBlockHintDbHandler::class)
                ->create($hint)
                ;
        }

        foreach ($hints->get('removed') as $hint) {
            $this->container
                ->get(TimeConditionBlockHintDbHandler::class)
                ->delete($hint)
                ;
        }
    }

    public function deleteTimeGroup(TimeConditionBlockEvent $event)
    {
        $timeConditionBlock = $event->getTimeConditionBlock();

        foreach ($timeConditionBlock->getTimeConditionBlockTgs() as $tg) {
            $this->container
                ->get(TimeConditionBlockTgDbHandler::class)
                ->delete($tg)
                ;
        }
    }

    public function deleteCalendar(TimeConditionBlockEvent $event)
    {
        $timeConditionBlock = $event->getTimeConditionBlock();

        foreach ($timeConditionBlock->getTimeConditionBlockCalendars() as $calendar) {
            $this->container
                ->get(TimeConditionBlockCalendarDbHandler::class)
                ->delete($calendar)
                ;
        }
    }

    public function deleteHint(TimeConditionBlockEvent $event)
    {
        $timeConditionBlock = $event->getTimeConditionBlock();

        foreach ($timeConditionBlock->getTimeConditionBlockHints() as $hint) {
            $this->container
                ->get(TimeConditionBlockHintDbHandler::class)
                ->delete($hint)
                ;
        }
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
