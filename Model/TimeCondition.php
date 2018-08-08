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

use Symfony\Component\Validator\Constraints as Assert;
use TelNowEdge\FreePBX\Base\Form\Model\Destination;

class TimeCondition
{
    protected $id;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @Assert\Type("numeric")
     */
    protected $internalDial;

    protected $dayNight;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $timezone;

    /**
     * @Assert\Type("\TelNowEdge\FreePBX\Base\Form\Model\Destination")
     * @Assert\NotNull()
     * @Assert\Valid(traverse=true)
     */
    protected $fallback;

    /**
     * @Assert\Type("\Doctrine\Common\Collections\ArrayCollection")
     * @Assert\Valid(traverse=true)
     * @Assert\All({
     *   @Assert\Type("\TelNowEdge\Module\tnetc\Model\TimeConditionBlock")
     * })
     */
    protected $timeConditionBlocks;

    public function __construct()
    {
        $this->dayNight = new DayNight();
        $this->fallback = new \TelNowEdge\FreePBX\Base\Form\Model\Destination();
        $this->timeConditionBlocks = new \Doctrine\Common\Collections\ArrayCollection(array(
            new TimeConditionBlock(true),
        ));
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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getInternalDial()
    {
        return $this->internalDial;
    }

    public function setInternalDial($internalDial)
    {
        $this->internalDial = $internalDial;

        return $this;
    }

    public function getDayNight()
    {
        return $this->dayNight;
    }

    /* PolyMorphy to prevent error because form allow null value. So when form is binded null */
    public function setDayNight($dayNight)
    {
        if (null === $dayNight) {
            $dayNight = new DayNight();
        }

        if (false === is_a($dayNight, DayNight::class)) {
            throw new \InvalidArgumentException();
        }

        $this->dayNight = $dayNight;

        return $this;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getFallback()
    {
        return $this->fallback;
    }

    public function getFallbackAsArray()
    {
        return explode(',', $this->fallback);
    }

    public function setFallback(Destination $fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }

    public function getTimeConditionBlocks()
    {
        return $this->timeConditionBlocks;
    }

    public function setTimeConditionBlocks(array $timeConditionBlocks)
    {
        foreach ($timeConditionBlocks as $timeConditionBlock) {
            $this->addTimeConditionBlock($timeConditionBlock);
        }

        return $this;
    }

    public function addTimeConditionBlock(TimeConditionBlock $timeConditionBlock)
    {
        if (true === $this->timeConditionBlocks->exists(function ($key, $object) use ($timeConditionBlock) {
            return $object->getId() === $timeConditionBlock->getId();
        }) && null !== $timeConditionBlock->getId()) {
            return $this;
        }

        $timeConditionBlock->setTimeCondition($this);

        $this->timeConditionBlocks->add($timeConditionBlock);

        return $this;
    }

    public function removeTimeConditionBlock(TimeConditionBlock $timeConditionBlock)
    {
        $this->timeConditionBlocks->removeElement($timeConditionBlock);

        return $this;
    }
}
