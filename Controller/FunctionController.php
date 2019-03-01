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

namespace TelNowEdge\Module\tnetc\Controller;

use TelNowEdge\FreePBX\Base\Controller\AbstractController;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\Module\tnetc\Manager\DestinationManager;
use TelNowEdge\Module\tnetc\Repository\TimeConditionRepository;

class FunctionController extends AbstractController
{
    public function getDestinations()
    {
        try {
            $timeConditions = $this
                ->get(TimeConditionRepository::class)
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            return null;
        }

        return array_map(function ($x) {
            return array(
                'destination' => sprintf('time-condition-tne,%d,1', $x->getId()),
                'description' => $x->getName(),
            );
        }, $timeConditions->toArray());
    }

    public function getDestinationInfo($dest)
    {
        if (0 === preg_match('/^time-condition-tne,([^,]+),/', $dest, $match)) {
            return false;
        }

        try {
            $timeCondition = $this
                ->get(TimeConditionRepository::class)
                ->getById($match[1])
                ;
        } catch (NoResultException $e) {
            return false;
        }

        return array(
            'description' => sprintf(_('TimeCondition %s : %s'), $timeCondition->getId(), $timeCondition->getName()),
            'edit_url' => sprintf('config.php?display=tnetc&id=%d', $timeCondition->getId()),
        );
    }

    public function checkDestinations($dest = true)
    {
        if ((true === \is_array($dest) && true === empty($dest))) {
            return array();
        }

        if (true === $dest) {
            return $this->get(DestinationManager::class)->getAll();
        }

        return $this->get(DestinationManager::class)->getByDestination($dest);
    }

    public static function getViewsDir()
    {
    }

    public static function getViewsNamespace()
    {
    }
}
