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

namespace TelNowEdge\Module\tnetc\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use TelNowEdge\FreePBX\Base\Form\Model\Destination;
use TelNowEdge\FreePBX\Base\Repository\AbstractRepository;
use TelNowEdge\Module\tnetc\Helper\CalendarHelper;
use TelNowEdge\Module\tnetc\Model\TimeCondition;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlock;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlockCalendar;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlockHint;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlockTg;

class TimeConditionRepository extends AbstractRepository
{
    const SQL = '
SELECT
        tc.id tc__id
        ,tc.name tc__name
        ,tc.internal_dial tc__internal_dial
        ,tc.daynight_id tc__daynight
        ,tc.timezone tc__timezone
        ,tc.fallback fallback__destination
        ,tcb.id tcb__id
        ,tcb.goto block__destination
        ,tcb.weight tcb__weight
        ,tcbtg.id tcbtg__id
        ,tcbtg.timegroups_groups_id tcbtg__time_group
        ,tcbc.id tcbc__id
        ,tcbc.policy tcbc__policy
        ,tcbc.calendar_id tcbc__calendar
        ,tcbh.id tcbh__id
        ,tcbh.type tcbh__type
    FROM
        tne_time_condition tc LEFT JOIN tne_time_condition_block tcb
            ON (
            tcb.tne_time_condition_id = tc.id
        ) LEFT JOIN tne_time_condition_block_tg tcbtg
            ON (
            tcbtg.tne_time_condition_block_id = tcb.id
        ) LEFT JOIN tne_time_condition_block_calendar tcbc
            ON (
            tcbc.tne_time_condition_block_id = tcb.id
        ) LEFT JOIN tne_time_condition_block_hint tcbh
            ON (
            tcbh.tne_time_condition_block_id = tcb.id
        )
';

    private $calendarHelper;

    private $timeGroupRepository;

    public function __construct(
        TimeGroupRepository $timeGroupRepository,
        CalendarHelper $calendarHelper
    ) {
        $this->calendarHelper = $calendarHelper;
        $this->timeGroupRepository = $timeGroupRepository;
    }

    public function getCollection()
    {
        $stmt = $this->connection->prepare(self::SQL);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        return $this
            ->collection($res)
            ;
    }

    public function getById($id)
    {
        $sql = sprintf('%s WHERE tc.id = :id', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        return $this
            ->collection($res)
            ->first()
            ;
    }

    private function collection(array $res)
    {
        $collection = new ArrayCollection();

        foreach ($res as $child) {
            $object = $this->mapModel($this->sqlToArray($child));

            if ($x = $collection->get($object->getId())) {
                $x->addTimeConditionBlock($object->getTimeConditionBlocks()->first());
                continue;
            }

            if (null === $object->getTimeConditionBlocks()->first()->getId()) {
                $object->getTimeConditionBlocks()->clear();
            }

            $collection->set($object->getId(), $object);
        }

        // Prevent that serialize was not an array but an object due to index
        return new ArrayCollection(
            $collection->getValues()
        );
    }

    private function mapModel(array $res)
    {
        $tc = $this->objectFromArray(TimeCondition::class, $res['tc']);
        $tcb = $this->objectFromArray(TimeConditionBlock::class, $res['tcb']);
        $tcbtg = $this->objectFromArray(TimeConditionBlockTg::class, $res['tcbtg']);
        $tcbc = $this->objectFromArray(TimeConditionBlockCalendar::class, $res['tcbc']);
        $tcbh = $this->objectFromArray(TimeConditionBlockHint::class, $res['tcbh']);
        $goto = $this->objectFromArray(Destination::class, $res['block']);
        $fallback = $this->objectFromArray(Destination::class, $res['fallback']);

        $tcb
            ->setGoto($goto)
            ->setTimeConditionBlockTgs(array($tcbtg))
            ->setTimeConditionBlockCalendars(array($tcbc))
            ->setTimeConditionBlockHints(array($tcbh))
            ;

        $tc
            ->setFallback($fallback)
            ->setTimeConditionBlocks(array($tcb))
            ;

        $this->fakeJoin($tc);

        return $tc;
    }

    private function fakeJoin(TimeCondition $tc)
    {
        foreach ($tc->getTimeConditionBlocks() as $block) {
            foreach ($block->getTimeConditionBlockTgs() as $x) {
                $res = $this->timeGroupRepository
                    ->getById($x->getTimeGroup())
                    ;

                $x->setTimeGroup($res);
            }

            foreach ($block->getTimeConditionBlockCalendars() as $x) {
                $res = $this->calendarHelper
                    ->getById($x->getCalendar())
                    ;

                $x->setCalendar($res);
            }
        }
    }
}
