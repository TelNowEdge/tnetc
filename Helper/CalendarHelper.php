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

namespace TelNowEdge\Module\tnetc\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use TelNowEdge\Module\tnetc\Model\Calendar;

class CalendarHelper
{
    public function getCollection()
    {
        $collection = new ArrayCollection();
        $list = \FreePBX::calendar()->listCalendars();

        foreach ($list as $k => $x) {
            $calendar = new Calendar();
            $calendar
                ->setId($k)
                ->setName($x['name'])
                ->setDescription($x['description'])
                ->setType($x['type'])
                ->setTimezone($x['timezone'])
                ;

            $collection->add($calendar);
        }

        return $collection;
    }

    public function getByType()
    {
        return $this
            ->getCollection()
            ->filter(function ($x) {
                return 'local' === $x->getType();
            });
    }

    public function getById($id)
    {
        return $this
            ->getCollection()
            ->filter(function ($x) use ($id) {
                return $id === $x->getId();
            })
            ->first()
            ;
    }
}
