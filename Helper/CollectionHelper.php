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

class CollectionHelper
{
    private static $instance;

    private $collection;

    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public static function create()
    {
        if (false === isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function addedItem($name, $item)
    {
        if (null === $this->collection->get($name)) {
            $this->collection->set(
                $name,
                new ArrayCollection(array(
                    'added' => new ArrayCollection(),
                    'removed' => new ArrayCollection(),
                ))
            );
        }

        $key = null === $item->getId() ? uniqid() : $item->getId();

        $this->collection
            ->get($name)
            ->get('added')
            ->set($key, $item)
            ;

        $this->collection
            ->get($name)
            ->get('removed')
            ->remove($item->getId())
            ;

        return $this;
    }

    public function removedItem($name, $item)
    {
        if (null === $this->collection->get($name)) {
            $this->collection->set(
                $name,
                new ArrayCollection(array(
                    'added' => new ArrayCollection(),
                    'removed' => new ArrayCollection(),
                ))
            );
        }

        $this->collection
            ->get($name)
            ->get('removed')
            ->set($item->getId(), $item)
            ;

        $this->collection
            ->get($name)
            ->get('added')
            ->remove($item->getId())
            ;
    }

    public function getByName($name)
    {
        if (null === $this->collection->get($name)) {
            throw new \InvalidArgumentException(sprintf('Unable to find collection: %s', $name));
        }

        $added = $this->collection
            ->get($name)
            ->get('added')
            ->filter(function ($x) {
                return null === $x->getId();
            });

        return new ArrayCollection(array(
            'removed' => $this->collection->get($name)->get('removed'),
            'added' => $added,
        ));
    }

    public function compileUpdated($name, ArrayCollection $collection)
    {
        $added = $this->collection
            ->get($name)
            ->get('added')
            ;

        return array_uintersect($collection->toArray(), $added->toArray(), function ($a, $b) {
            if ($a->getId() === $b->getId()) {
                return null === $a->getId() ? -1 : 0;
            }

            return $a->getId() < $b->getId() ? -1 : 1;
        });
    }

    public function clear()
    {
        $this->collection->clear();

        return $this;
    }
}
