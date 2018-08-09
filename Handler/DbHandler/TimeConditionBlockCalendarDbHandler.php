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

namespace TelNowEdge\Module\tnetc\Handler\DbHandler;

use TelNowEdge\FreePBX\Base\Handler\AbstractDbHandler;
use TelNowEdge\Module\tnetc\Event\TimeConditionBlockCalendarEvent;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlockCalendar;

class TimeConditionBlockCalendarDbHandler extends AbstractDbHandler
{
    public function create(TimeConditionBlockCalendar $timeConditionBlockCalendar)
    {
        $sql = '
INSERT
    INTO
        `tne_time_condition_block_calendar` (
            `policy`
            ,`tne_time_condition_block_id`
            ,`calendar_id`
        )
    VALUES (
        :policy
        ,:timeConditionBlock
        ,:calendar
    )
';

        $this->eventDispatcher->dispatch(
            TimeConditionBlockCalendarEvent::CREATE_PRE_BIND,
            new TimeConditionBlockCalendarEvent($timeConditionBlockCalendar)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('policy', $timeConditionBlockCalendar->getPolicy());
        $stmt->bindParam('timeConditionBlock', $timeConditionBlockCalendar->getTimeConditionBlock()->getId());
        $stmt->bindParam('calendar', $timeConditionBlockCalendar->getCalendar()->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockCalendarEvent::CREATE_PRE_SAVE,
            new TimeConditionBlockCalendarEvent($timeConditionBlockCalendar)
        );

        $stmt->execute();

        $timeConditionBlockCalendar->setId($this->connection->lastInsertId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockCalendarEvent::CREATE_POST_SAVE,
            new TimeConditionBlockCalendarEvent($timeConditionBlockCalendar)
        );

        return true;
    }

    public function update(TimeConditionBlockCalendar $timeConditionBlockCalendar)
    {
        $sql = '
UPDATE
        `tne_time_condition_block_calendar`
    SET
        `policy` = :policy
        ,`tne_time_condition_block_id` = :timeConditionBlock
        ,`calendar_id` = :calendar
    WHERE
        id = :id
';

        $this->eventDispatcher->dispatch(
            TimeConditionBlockCalendarEvent::UPDATE_PRE_BIND,
            new TimeConditionBlockCalendarEvent($timeConditionBlockCalendar)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $timeConditionBlockCalendar->getId());
        $stmt->bindParam('policy', $timeConditionBlockCalendar->getPolicy());
        $stmt->bindParam('timeConditionBlock', $timeConditionBlockCalendar->getTimeConditionBlock()->getId());
        $stmt->bindParam('calendar', $timeConditionBlockCalendar->getCalendar()->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockCalendarEvent::UPDATE_PRE_SAVE,
            new TimeConditionBlockCalendarEvent($timeConditionBlockCalendar)
        );

        /* $stmt->execute(); */

        $this->eventDispatcher->dispatch(
            TimeConditionBlockCalendarEvent::UPDATE_POST_SAVE,
            new TimeConditionBlockCalendarEvent($timeConditionBlockCalendar)
        );

        return true;
    }

    public function delete(TimeConditionBlockCalendar $timeConditionBlockCalendar)
    {
        $sql = '
DELETE
    FROM
        `tne_time_condition_block_calendar`
    WHERE
        id = :id
';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $timeConditionBlockCalendar->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockCalendarEvent::DELETE_PRE_SAVE,
            new TimeConditionBlockCalendarEvent($timeConditionBlockCalendar)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            TimeConditionBlockCalendarEvent::DELETE_POST_SAVE,
            new TimeConditionBlockCalendarEvent($timeConditionBlockCalendar)
        );

        return true;
    }
}
