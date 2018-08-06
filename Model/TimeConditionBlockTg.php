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

namespace TelNowEdge\Module\tnetc\Model;

class TimeConditionBlockTg
{
    use TimeConditionBlockTrait;

    protected $id;

    protected $timeConditionBlock;

    protected $timeGroup;

    public function __construct()
    {
        $this->timeConditionBlock = new TimeConditionBlock();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getTimeGroup()
    {
        return $this->timeGroup;
    }

    public function setTimeGroup($timeGroup)
    {
        $this->timeGroup = $timeGroup;

        return $this;
    }
}
