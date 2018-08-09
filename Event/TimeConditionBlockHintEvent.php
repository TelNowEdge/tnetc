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

namespace TelNowEdge\Module\tnetc\Event;

use Symfony\Component\EventDispatcher\Event;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlockHint;

class TimeConditionBlockHintEvent extends Event
{
    const CREATE_PRE_BIND = 'time_condition_block_hint.create.pre_bind';
    const CREATE_PRE_SAVE = 'time_condition_block_hint.create.pre_save';
    const CREATE_POST_SAVE = 'time_condition_block_hint.create.post_save';

    const UPDATE_PRE_BIND = 'time_condition_block_hint.update.pre_bind';
    const UPDATE_PRE_SAVE = 'time_condition_block_hint.update.pre_save';
    const UPDATE_POST_SAVE = 'time_condition_block_hint.update.post_save';

    const DELETE_PRE_SAVE = 'time_condition_block_hint.delete.pre_save';
    const DELETE_POST_SAVE = 'time_condition_block_hint.delete.post_save';

    const DUPLICATE_PRE_SAVE = 'time_condition_block_hint.duplicate.pre_save';
    const DUPLICATE_POST_SAVE = 'time_condition_block_hint.duplicate.post_save';

    private $timeConditionBlockHint;

    public function __construct(TimeConditionBlockHint $timeConditionBlockHint)
    {
        $this->timeConditionBlockHint = $timeConditionBlockHint;
    }

    public function getTimeConditionBlockHint()
    {
        return $this->timeConditionBlockHint;
    }
}
