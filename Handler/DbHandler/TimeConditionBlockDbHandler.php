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
use TelNowEdge\Module\tnetc\Event\TimeConditionBlockEvent;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlock;

class TimeConditionBlockDbHandler extends AbstractDbHandler
{
    public function create(TimeConditionBlock $timeConditionBlock)
    {
        $sql = '
INSERT
    INTO
        `tne_time_condition_block` (
            `goto`
            ,`tne_time_condition_id`
            ,`weight`
        )
    VALUES (
        :goto
        ,:timeCondition
        ,:weight
    )
';

        $this->eventDispatcher->dispatch(
            TimeConditionBlockEvent::CREATE_PRE_BIND,
            new TimeConditionBlockEvent($timeConditionBlock)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('goto', $timeConditionBlock->getGoto()->getDestination());
        $stmt->bindParam('timeCondition', $timeConditionBlock->getTimeCondition()->getId());
        $stmt->bindParam('weight', $timeConditionBlock->getWeight());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockEvent::CREATE_PRE_SAVE,
            new TimeConditionBlockEvent($timeConditionBlock)
        );

        $stmt->execute();

        $timeConditionBlock->setId($this->connection->lastInsertId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockEvent::CREATE_POST_SAVE,
            new TimeConditionBlockEvent($timeConditionBlock)
        );

        return true;
    }

    public function update(TimeConditionBlock $timeConditionBlock)
    {
        $sql = '
UPDATE
        `tne_time_condition_block`
    SET
        goto = :goto
        ,tne_time_condition_id = :timeCondition
        ,weight = :weight
    WHERE
        id = :id
';

        $this->eventDispatcher->dispatch(
            TimeConditionBlockEvent::UPDATE_PRE_BIND,
            new TimeConditionBlockEvent($timeConditionBlock)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $timeConditionBlock->getId());
        $stmt->bindParam('goto', $timeConditionBlock->getGoto()->getDestination());
        $stmt->bindParam('timeCondition', $timeConditionBlock->getTimeCondition()->getId());
        $stmt->bindParam('weight', $timeConditionBlock->getWeight());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockEvent::UPDATE_PRE_SAVE,
            new TimeConditionBlockEvent($timeConditionBlock)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            TimeConditionBlockEvent::UPDATE_POST_SAVE,
            new TimeConditionBlockEvent($timeConditionBlock)
        );

        return true;
    }

    public function delete(TimeConditionBlock $timeConditionBlock)
    {
        $sql = '
DELETE
    FROM
        `tne_time_condition_block`
    WHERE
        id = :id
';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $timeConditionBlock->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockEvent::DELETE_PRE_SAVE,
            new TimeConditionBlockEvent($timeConditionBlock)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            TimeConditionBlockEvent::DELETE_POST_SAVE,
            new TimeConditionBlockEvent($timeConditionBlock)
        );

        return true;
    }
}
