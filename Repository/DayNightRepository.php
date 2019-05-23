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
use TelNowEdge\Module\tnetc\Model\DayNight;

class DayNightRepository extends AbstractRepository
{
    const SQL = '
SELECT
        d.ext d__ext
        ,d.dmode d__dmode
        ,d.dest d__dest
    FROM
        daynight d
';

    public function getCollection()
    {
        $collection = new ArrayCollection();

        $stmt = $this->connection->prepare(self::SQL);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        $unlinearize = $this->unlinearize($res);

        foreach ($unlinearize as $child) {
            $res = $this->mapModel($this->sqlToArray($child));
            $collection->add($res);
        }

        return $collection;
    }

    public function getByExt($ext)
    {
        $collection = new ArrayCollection();
        $sql = sprintf('%s WHERE ext = :ext', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('ext', $ext);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        $unlinearize = $this->unlinearize($res);

        foreach ($unlinearize as $child) {
            $res = $this->mapModel($this->sqlToArray($child));
            $collection->add($res);
        }

        return $collection
            ->first()
            ;
    }

    private function unlinearize(array $res)
    {
        $tab = array();

        foreach ($res as $child) {
            if (false === isset($tab[$child->d__ext])) {
                $tab[$child->d__ext] = array();
            }

            $field = sprintf('d__%s', $child->d__dmode);
            $tab[$child->d__ext] = array_merge(
                $tab[$child->d__ext],
                array('d__ext' => $child->d__ext, $field => $child->d__dest)
            );
        }

        return $tab;
    }

    private function mapModel(array $res)
    {
        /* Beautiful code was never writing */
        $res['dest_n']['destination'] = $res['d']['night'];
        $res['dest_d']['destination'] = $res['d']['day'];
        unset(
            $res['d']['night'],
            $res['d']['day']
        );

        $d = $this->objectFromArray(DayNight::class, $res['d']);
        $night = $this->objectFromArray(Destination::class, $res['dest_n']);
        $day = $this->objectFromArray(Destination::class, $res['dest_d']);

        $d
            ->setNight($night)
            ->setDay($day)
            ;

        return $d;
    }
}
