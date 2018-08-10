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
use TelNowEdge\FreePBX\Base\Repository\AbstractRepository;
use TelNowEdge\Module\tnetc\Model\TimeGroup;

class TimeGroupRepository extends AbstractRepository
{
    const SQL = '
SELECT
        tg.id tg__id
        ,tg.description tg__description
    FROM
        timegroups_groups tg
';

    public function getCollection()
    {
        $stmt = $this->connection->prepare(self::SQL);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        return $this->collection($res);
    }

    public function getById($id)
    {
        $sql = sprintf('%s WHERE tg.id = :id', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $id);

        $stmt->execute();

        return $this->mapModel(
            $this->sqlToArray(
                $this->fetch($stmt)
            )
        );
    }

    private function collection(array $res)
    {
        $collection = new ArrayCollection();

        foreach ($res as $child) {
            $object = $this->mapModel($this->sqlToArray($child));
            $collection->set($object->getId(), $object);
        }

        // Prevent that serialize was not an array but an object due to index
        return new ArrayCollection(
            $collection->getValues()
        );
    }

    private function mapModel(array $res)
    {
        return $this->objectFromArray(TimeGroup::class, $res['tg']);
    }
}
