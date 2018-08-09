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
use TelNowEdge\Module\tnetc\Event\TimeConditionBlockHintEvent;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlockHint;

class TimeConditionBlockHintDbHandler extends AbstractDbHandler
{
    public function create(TimeConditionBlockHint $timeConditionBlockHint)
    {
        $sql = '
INSERT
    INTO
        `tne_time_condition_block_hint` (
            `type`
            ,`tne_time_condition_block_id`
        )
    VALUES (
        :type
        ,:timeConditionBlock
    )
';

        $this->eventDispatcher->dispatch(
            TimeConditionBlockHintEvent::CREATE_PRE_BIND,
            new TimeConditionBlockHintEvent($timeConditionBlockHint)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('type', $timeConditionBlockHint->getType());
        $stmt->bindParam('timeConditionBlock', $timeConditionBlockHint->getTimeConditionBlock()->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockHintEvent::CREATE_PRE_SAVE,
            new TimeConditionBlockHintEvent($timeConditionBlockHint)
        );

        $stmt->execute();

        $timeConditionBlockHint->setId($this->connection->lastInsertId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockHintEvent::CREATE_POST_SAVE,
            new TimeConditionBlockHintEvent($timeConditionBlockHint)
        );

        return true;
    }

    public function update(TimeConditionBlockHint $timeConditionBlockHint)
    {
        $sql = '
UPDATE
        `tne_time_condition_block_hint`
    SET
        `type` = :type
        ,`tne_time_condition_block_id` = :timeConditionBlock
    WHERE
        id = :id
';

        $this->eventDispatcher->dispatch(
            TimeConditionBlockHintEvent::UPDATE_PRE_BIND,
            new TimeConditionBlockHintEvent($timeConditionBlockHint)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $timeConditionBlockHint->getId());
        $stmt->bindParam('type', $timeConditionBlockHint->getType());
        $stmt->bindParam('timeConditionBlock', $timeConditionBlockHint->getTimeConditionBlock()->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockHintEvent::UPDATE_PRE_SAVE,
            new TimeConditionBlockHintEvent($timeConditionBlockHint)
        );

        /* $stmt->execute(); */

        $this->eventDispatcher->dispatch(
            TimeConditionBlockHintEvent::UPDATE_POST_SAVE,
            new TimeConditionBlockHintEvent($timeConditionBlockHint)
        );

        return true;
    }

    public function delete(TimeConditionBlockHint $timeConditionBlockHint)
    {
        $sql = '
DELETE
    FROM
        `tne_time_condition_block_hint`
    WHERE
        id = :id
';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $timeConditionBlockHint->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockHintEvent::DELETE_PRE_SAVE,
            new TimeConditionBlockHintEvent($timeConditionBlockHint)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            TimeConditionBlockHintEvent::DELETE_POST_SAVE,
            new TimeConditionBlockHintEvent($timeConditionBlockHint)
        );

        return true;
    }
}
