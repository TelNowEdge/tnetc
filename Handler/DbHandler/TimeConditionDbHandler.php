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
use TelNowEdge\Module\tnetc\Event\TimeConditionEvent;
use TelNowEdge\Module\tnetc\Model\TimeCondition;

class TimeConditionDbHandler extends AbstractDbHandler
{
    public function create(TimeCondition $timeCondition)
    {
        $sql = '
INSERT
    INTO
        `tne_time_condition` (
            `name`
            ,`internal_dial`
            ,`daynight_id`
            ,`timezone`
            ,`fallback`
        )
    VALUES (
        :name
        ,:internalDial
        ,:daynight
        ,:timezone
        ,:fallback
    )
';

        $this->eventDispatcher->dispatch(
            TimeConditionEvent::CREATE_PRE_BIND,
            new TimeConditionEvent($timeCondition)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('name', $timeCondition->getName());
        $stmt->bindParam('internalDial', $timeCondition->getInternalDial());
        $stmt->bindParam('daynight', $timeCondition->getDayNight()->getExt());
        $stmt->bindParam('timezone', $timeCondition->getTimezone());
        $stmt->bindParam('fallback', $timeCondition->getFallback()->getDestination());

        $this->eventDispatcher->dispatch(
            TimeConditionEvent::CREATE_PRE_SAVE,
            new TimeConditionEvent($timeCondition)
        );

        $stmt->execute();

        $timeCondition->setId($this->connection->lastInsertId());

        $this->eventDispatcher->dispatch(
            TimeConditionEvent::CREATE_POST_SAVE,
            new TimeConditionEvent($timeCondition)
        );

        return true;
    }

    public function update(TimeCondition $timeCondition)
    {
        $sql = '
UPDATE
        `tne_time_condition`
    SET
        name = :name
        ,internal_dial = :internalDial
        ,daynight_id = :daynight
        ,timezone = :timezone
        ,fallback = :fallback
    WHERE
        id = :id
';

        $this->eventDispatcher->dispatch(
            TimeConditionEvent::UPDATE_PRE_BIND,
            new TimeConditionEvent($timeCondition)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $timeCondition->getId());
        $stmt->bindParam('name', $timeCondition->getName());
        $stmt->bindParam('internalDial', $timeCondition->getInternalDial());
        $stmt->bindParam('daynight', $timeCondition->getDayNight()->getId());
        $stmt->bindParam('timezone', $timeCondition->getTimezone());
        $stmt->bindParam('fallback', $timeCondition->getFallback()->getDestination());

        $this->eventDispatcher->dispatch(
            TimeConditionEvent::UPDATE_PRE_SAVE,
            new TimeConditionEvent($timeCondition)
        );

        $stmt->execute();

        $timeCondition->setId($this->connection->lastInsertId());

        $this->eventDispatcher->dispatch(
            TimeConditionEvent::UPDATE_POST_SAVE,
            new TimeConditionEvent($timeCondition)
        );

        return true;
    }

    public function delete(TimeCondition $timeCondition)
    {
        $sql = '
DELETE
    FROM
        `tne_time_condition`
    WHERE
        id = :id
';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $timeCondition->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionEvent::DELETE_PRE_SAVE,
            new TimeConditionEvent($timeCondition)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            TimeConditionEvent::DELETE_POST_SAVE,
            new TimeConditionEvent($timeCondition)
        );

        return true;
    }
}
