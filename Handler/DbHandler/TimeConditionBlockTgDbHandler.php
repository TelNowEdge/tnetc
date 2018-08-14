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
use TelNowEdge\Module\tnetc\Event\TimeConditionBlockTgEvent;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlockTg;

class TimeConditionBlockTgDbHandler extends AbstractDbHandler
{
    public function create(TimeConditionBlockTg $timeConditionBlockTg)
    {
        $sql = '
INSERT
    INTO
        `tne_time_condition_block_tg` (
            `tne_time_condition_block_id`
            ,`timegroups_groups_id`
        )
    VALUES (
        :timeConditionBlock
        ,:timeGroup
    )
';

        $this->eventDispatcher->dispatch(
            TimeConditionBlockTgEvent::CREATE_PRE_BIND,
            new TimeConditionBlockTgEvent($timeConditionBlockTg)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('timeConditionBlock', $timeConditionBlockTg->getTimeConditionBlock()->getId());
        $stmt->bindParam('timeGroup', $timeConditionBlockTg->getTimeGroup()->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockTgEvent::CREATE_PRE_SAVE,
            new TimeConditionBlockTgEvent($timeConditionBlockTg)
        );

        $stmt->execute();

        $timeConditionBlockTg->setId($this->connection->lastInsertId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockTgEvent::CREATE_POST_SAVE,
            new TimeConditionBlockTgEvent($timeConditionBlockTg)
        );

        return true;
    }

    public function update(TimeConditionBlockTg $timeConditionBlockTg)
    {
        $sql = '
UPDATE
        `tne_time_condition_block_tg`
    SET
        `tne_time_condition_block_id` = :timeConditionBlock
        ,`timegroups_groups_id` = :timeGroup
    WHERE
        id = :id
';

        $this->eventDispatcher->dispatch(
            TimeConditionBlockTgEvent::UPDATE_PRE_BIND,
            new TimeConditionBlockTgEvent($timeConditionBlockTg)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $timeConditionBlockTg->getId());
        $stmt->bindParam('timeConditionBlock', $timeConditionBlockTg->getTimeConditionBlock()->getId());
        $stmt->bindParam('timeGroup', $timeConditionBlockTg->getTimeGroup()->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockTgEvent::UPDATE_PRE_SAVE,
            new TimeConditionBlockTgEvent($timeConditionBlockTg)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            TimeConditionBlockTgEvent::UPDATE_POST_SAVE,
            new TimeConditionBlockTgEvent($timeConditionBlockTg)
        );

        return true;
    }

    public function delete(TimeConditionBlockTg $timeConditionBlockTg)
    {
        $sql = '
DELETE
    FROM
        `tne_time_condition_block_tg`
    WHERE
        id = :id
';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $timeConditionBlockTg->getId());

        $this->eventDispatcher->dispatch(
            TimeConditionBlockTgEvent::DELETE_PRE_SAVE,
            new TimeConditionBlockTgEvent($timeConditionBlockTg)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            TimeConditionBlockTgEvent::DELETE_POST_SAVE,
            new TimeConditionBlockTgEvent($timeConditionBlockTg)
        );

        return true;
    }
}
