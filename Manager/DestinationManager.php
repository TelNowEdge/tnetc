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

namespace TelNowEdge\Module\tnetc\Manager;

use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\Module\tnetc\Repository\TimeConditionRepository;

class DestinationManager
{
    /**
     * TelNowEdge\Module\tnetc\Repository\TimeConditionRepository.
     */
    private $timeConditionRepository;

    public function __construct(TimeConditionRepository $timeConditionRepository)
    {
        $this->timeConditionRepository = $timeConditionRepository;
    }

    public function getAll()
    {
        $output = array();

        try {
            $timeConditions = $this->timeConditionRepository
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            return array();
        }

        foreach ($timeConditions as $timeCondition) {
            foreach ($timeCondition->getTimeConditionBlocks() as $timeConditionBlock) {
                array_push($output, array(
                    'dest' => $timeConditionBlock->getGoto()->getDestination(),
                    'description' => sprintf(
                        'Time condition: [%s] %s',
                        $timeConditionBlock->getWeight(),
                        $timeCondition->getName()
                    ),
                    'edit_url' => sprintf('config.php?display=tnetc&id=%d', $timeCondition->getId()),
                ));
            }

            array_push($output, array(
                'dest' => $timeCondition->getFallback()->getDestination(),
                'description' => sprintf(
                    'Time condition: [%s] %s',
                    'Fallback',
                    $timeCondition->getName()
                ),
                'edit_url' => sprintf('config.php?display=tnetc&id=%d', $timeCondition->getId()),
            ));
        }

        return $output;
    }

    public function getByDestination(array $destination)
    {
        $timeConditions = array();
        $fallbacks = array();
        $output = array();

        try {
            $timeConditions = $this->timeConditionRepository
                ->getByGotos($destination)
                ;
        } catch (NoResultException $e) {
            // Do nothing
        }

        try {
            $fallbacks = $this->timeConditionRepository
                ->getByFallBack($destination)
                ;
        } catch (NoResultException $e) {
            // Do nothing
        }

        foreach ($timeConditions as $timeCondition) {
            foreach ($timeCondition->getTimeConditionBlocks() as $timeConditionBlock) {
                foreach ($destination as $x) {
                    if ($timeConditionBlock->getGoto()->getDestination() !== $x) {
                        continue;
                    }

                    array_push($output, array(
                        'dest' => $timeConditionBlock->getGoto()->getDestination(),
                        'description' => sprintf(
                            'Time condition: [%s] %s',
                            $timeConditionBlock->getWeight(),
                            $timeCondition->getName()
                        ),
                        'edit_url' => sprintf('config.php?display=tnetc&id=%d', $timeCondition->getId()),
                    ));
                }
            }
        }

        foreach ($fallbacks as $timeCondition) {
            foreach ($destination as $x) {
                if ($timeCondition->getFallback()->getDestination() !== $x) {
                    continue;
                }

                array_push($output, array(
                    'dest' => $timeCondition->getFallback()->getDestination(),
                    'description' => sprintf(
                        'Time condition: [%s] %s',
                        'Fallback',
                        $timeCondition->getName()
                    ),
                    'edit_url' => sprintf('config.php?display=tnetc&id=%d', $timeCondition->getId()),
                ));
            }
        }

        return $output;
    }
}
