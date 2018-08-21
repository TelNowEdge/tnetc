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
use TelNowEdge\Module\tnetc\Helper\CollectionHelper;

class TimeConditionBlock
{
    protected $id;

    /**
     * @Assert\Type("\TelNowEdge\FreePBX\Base\Form\Model\Destination")
     * @Assert\NotNull()
     * @Assert\Valid(traverse=true)
     */
    protected $goto;

    /**
     * @Assert\Type("numeric")
     * @Assert\NotNull()
     */
    protected $weight;

    /**
     * @Assert\Type("\TelNowEdge\Module\tnetc\Model\TimeCondition")
     */
    protected $timeCondition;

    /**
     * @Assert\Type("\Doctrine\Common\Collections\ArrayCollection")
     * @Assert\Valid(traverse=true)
     * @Assert\All({
     *   @Assert\Type("\TelNowEdge\Module\tnetc\Model\TimeConditionBlockTg")
     * })
     */
    protected $timeConditionBlockTgs;

    /**
     * @Assert\Type("\Doctrine\Common\Collections\ArrayCollection")
     * @Assert\Valid(traverse=true)
     * @Assert\All({
     *   @Assert\Type("\TelNowEdge\Module\tnetc\Model\TimeConditionBlockCalendar")
     * })
     */
    protected $timeConditionBlockCalendars;

    /**
     * @Assert\Type("\Doctrine\Common\Collections\ArrayCollection")
     * @Assert\Valid(traverse=true)
     * @Assert\All({
     *   @Assert\Type("\TelNowEdge\Module\tnetc\Model\TimeConditionBlockHint")
     * })
     * @Assert\Count(
     *   max=1,
     *   maxMessage="Only 1 BLF by block"
     * )
     */
    protected $timeConditionBlockHints;

    protected $isActive;

    public function __construct($child = false)
    {
        $this->weight = 0;
        $this->timeConditionBlockTgs = new \Doctrine\Common\Collections\ArrayCollection(array(
            new TimeConditionBlockTg(true),
        ));
        $this->timeConditionBlockCalendars = new \Doctrine\Common\Collections\ArrayCollection();
        $this->timeConditionBlockHints = new \Doctrine\Common\Collections\ArrayCollection();

        if (true === $child) {
            return;
        }
        $this->goto = new Destination();
        $this->timeCondition = new TimeCondition();
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

    public function getGoto()
    {
        return $this->goto;
    }

    public function setGoto(Destination $goto)
    {
        $this->goto = $goto;

        return $this;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    public function getTimeCondition()
    {
        return $this->timeCondition;
    }

    public function setTimeCondition(TimeCondition $timeCondition)
    {
        $this->timeCondition = $timeCondition;

        return $this;
    }

    public function getTimeConditionBlockTgs()
    {
        return $this->timeConditionBlockTgs;
    }

    public function setTimeConditionBlockTgs(array $timeConditionBlockTgs)
    {
        $this->timeConditionBlockTgs->clear();

        foreach ($timeConditionBlockTgs as $timeConditionBlockTg) {
            $this->addTimeConditionBlockTg($timeConditionBlockTg);
        }

        return $this;
    }

    public function addTimeConditionBlockTg(TimeConditionBlockTg $timeConditionBlockTg)
    {
        if (null === $timeConditionBlockTg->getTimeGroup()) {
            return $this;
        }

        if (true === $this->timeConditionBlockTgs->exists(
            function ($key, $object) use ($timeConditionBlockTg) {
                return $object->getId() === $timeConditionBlockTg->getId();
            }) && null !== $timeConditionBlockTg->getId()
        ) {
            return $this;
        }

        $timeConditionBlockTg->setTimeConditionBlock($this);

        $this->timeConditionBlockTgs->add($timeConditionBlockTg);

        CollectionHelper::create()
            ->addedItem(
                sprintf('tg%d', $this->id),
                $timeConditionBlockTg
            );

        return $this;
    }

    public function removeTimeConditionBlockTg(TimeConditionBlockTg $timeConditionBlockTg)
    {
        $this->timeConditionBlockTgs->removeElement($timeConditionBlockTg);

        CollectionHelper::create()
            ->removedItem(
                sprintf('tg%d', $this->id),
                $timeConditionBlockTg
            );

        return $this;
    }

    public function getTimeConditionBlockCalendars()
    {
        return $this->timeConditionBlockCalendars;
    }

    public function setTimeConditionBlockCalendars(array $timeConditionBlockCalendars)
    {
        foreach ($timeConditionBlockCalendars as $timeConditionBlockCalendar) {
            $this->addTimeConditionBlockCalendar($timeConditionBlockCalendar);
        }

        return $this;
    }

    public function addTimeConditionBlockCalendar(TimeConditionBlockCalendar $timeConditionBlockCalendar)
    {
        if (null === $timeConditionBlockCalendar->getCalendar()) {
            return $this;
        }

        if (true === $this->timeConditionBlockCalendars->exists(
            function ($key, $object) use ($timeConditionBlockCalendar) {
                return $object->getId() === $timeConditionBlockCalendar->getId();
            }) && null !== $timeConditionBlockCalendar->getId()
        ) {
            return $this;
        }

        $timeConditionBlockCalendar->setTimeConditionBlock($this);

        $this->timeConditionBlockCalendars->add($timeConditionBlockCalendar);

        CollectionHelper::create()
            ->addedItem(
                sprintf('calendar%d', $this->id),
                $timeConditionBlockCalendar
            );

        return $this;
    }

    public function removeTimeConditionBlockCalendar(TimeConditionBlockCalendar $timeConditionBlockCalendar)
    {
        $this->timeConditionBlockCalendars->removeElement($timeConditionBlockCalendar);

        CollectionHelper::create()
            ->removedItem(
                sprintf('calendar%d', $this->id),
                $timeConditionBlockCalendar
            );

        return $this;
    }

    public function getTimeConditionBlockHints()
    {
        return $this->timeConditionBlockHints;
    }

    public function setTimeConditionBlockHints(array $timeConditionBlockHints)
    {
        foreach ($timeConditionBlockHints as $timeConditionBlockHint) {
            $this->addTimeConditionBlockHint($timeConditionBlockHint);
        }

        return $this;
    }

    public function addTimeConditionBlockHint(TimeConditionBlockHint $timeConditionBlockHint)
    {
        if (null === $timeConditionBlockHint->getType()) {
            return $this;
        }

        if (true === $this->timeConditionBlockHints->exists(
            function ($key, $object) use ($timeConditionBlockHint) {
                return $object->getId() === $timeConditionBlockHint->getId();
            }) && null !== $timeConditionBlockHint->getId()
        ) {
            return $this;
        }

        $timeConditionBlockHint->setTimeConditionBlock($this);

        $this->timeConditionBlockHints->add($timeConditionBlockHint);

        CollectionHelper::create()
            ->addedItem(
                sprintf('hint%d', $this->id),
                $timeConditionBlockHint
            );

        return $this;
    }

    public function removeTimeConditionBlockHint(TimeConditionBlockHint $timeConditionBlockHint)
    {
        $this->timeConditionBlockHints->removeElement($timeConditionBlockHint);

        CollectionHelper::create()
            ->removedItem(
                sprintf('hint%d', $this->id),
                $timeConditionBlockHint
            );

        return $this;
    }

    public function getIsActive()
    {
        foreach ($this->getTimeConditionBlockTgs() as $blockTg) {
            if (null === $blockTg->getTimeGroup()) {
                continue;
            }

            foreach ($blockTg->getTimeGroup()->getTimes() as $time) {
                if (5 > count(explode('|', $time))) {
                    $time = sprintf('%s|*', $time);
                }

                if (true === \FreePBX::Timeconditions()->checkTime($time)) {
                    return true;
                }
            }
        }

        foreach ($this->getTimeConditionBlockCalendars() as $blockCalendar) {
            if (null === $blockCalendar->getCalendar()) {
                continue;
            }

            $match = \FreePBX::Calendar()->matchCalendar($blockCalendar->getCalendar()->getId());

            if (true === $match && 'straight' === $blockCalendar->getPolicy()) {
                return true;
            }

            if (false === $match && 'inverse' === $blockCalendar->getPolicy()) {
                return true;
            }
        }

        return false;
    }

    public function setIsActive($active)
    {
        return $this;
    }
}
