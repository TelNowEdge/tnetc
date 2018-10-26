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

use Doctrine\Common\Collections\ArrayCollection;
use TelNowEdge\FreePBX\Base\Controller\AbstractController;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\Module\tnetc\Handler\DbHandler\TimeConditionDbHandler;
use TelNowEdge\Module\tnetc\Repository\TimeConditionRepository;

class AjaxController extends AbstractController
{
    public function getTimeConditions()
    {
        try {
            $collection = $this
                ->get(TimeConditionRepository::class)
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            $collection = new ArrayCollection();
        }

        return $this->get('serializer')->normalize($collection);
    }

    public function search($query, &$results)
    {
        try {
            $timeConditions = $this
                ->get(TimeConditionRepository::class)
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            return array();
        }

        foreach ($timeConditions as $timeCondition) {
            array_push($results, array(
                'text' => sprintf('[Time condition] %s', $timeCondition->getName()),
                'type' => 'get',
                'dest' => sprintf('?display=tnetc&id=%d', $timeCondition->getId()),
            ));
            array_push($results, array(
                'text' => sprintf('[Time condition][%s] %s', $timeCondition->getName(), $timeCondition->getInternalDial()),
                'type' => 'get',
                'dest' => sprintf('?display=tnetc&id=%d', $timeCondition->getId()),
            ));
        }
    }

    public function delete()
    {
        $request = $this->get('request');

        $id = $request->request->getInt('id', 0);

        if (0 >= $id) {
            return false;
        }

        try {
            $timeCondition = $this
                ->get(TimeConditionRepository::class)
                ->getById($id)
                ;
        } catch (NoResultException $e) {
            return $this->get('serializer')->normalize(array('success' => false));
        }

        $this->get(TimeConditionDbHandler::class)
             ->delete($timeCondition)
            ;

        return $this->get('serializer')->normalize(array('success' => true));
    }

    public static function getViewsDir()
    {
        return sprintf('%s/../views', __DIR__);
    }

    public static function getViewsNamespace()
    {
        return 'tnetc';
    }
}
