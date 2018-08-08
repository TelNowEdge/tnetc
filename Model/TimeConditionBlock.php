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

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use TelNowEdge\FreePBX\Base\Form\Model\Destination;

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
        $this->timeConditionBlockTgs = new ArrayCollection();
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
        foreach ($timeConditionBlockTgs as $timeConditionBlockTg) {
            $this->addTimeConditionBlockTg($timeConditionBlockTg);
        }

        return $this;
    }

    public function addTimeConditionBlockTg(TimeConditionBlockTg $timeConditionBlockTg)
    {
        if (true === $this->timeConditionBlockTgs->exists(function ($key, $object) use ($timeConditionBlockTg) {
            return $object->getId() === $timeConditionBlockTg->getId();
        }) && null !== $timeConditionBlockTg->getId()) {
            return $this;
        }

        $timeConditionBlockTg->setTimeConditionBlock($this);

        $this->timeConditionBlockTgs->add($timeConditionBlockTg);

        return $this;
    }

    public function removeTimeConditionBlockTg(TimeConditionBlockTg $timeConditionBlockTg)
    {
        $this->timeConditionBlockTgs->removeElement($timeConditionBlockTg);

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
        if (true === $this->timeConditionBlockCalendars->exists(function ($key, $object) use ($timeConditionBlockCalendar) {
            return $object->getId() === $timeConditionBlockCalendar->getId();
        }) && null !== $timeConditionBlockCalendar->getId()) {
            return $this;
        }

        $timeConditionBlockCalendar->setTimeConditionBlock($this);

        $this->timeConditionBlockCalendars->add($timeConditionBlockCalendar);

        return $this;
    }

    public function removeTimeConditionBlockCalendar(TimeConditionBlockCalendar $timeConditionBlockCalendar)
    {
        $this->timeConditionBlockCalendars->removeElement($timeConditionBlockCalendar);

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
        if (true === $this->timeConditionBlockHints->exists(function ($key, $object) use ($timeConditionBlockHint) {
            return $object->getId() === $timeConditionBlockHint->getId();
        }) && null !== $timeConditionBlockHint->getId()) {
            return $this;
        }

        $timeConditionBlockHint->setTimeConditionBlock($this);

        $this->timeConditionBlockHints->add($timeConditionBlockHint);

        return $this;
    }

    public function removeTimeConditionBlockHint(TimeConditionBlockHint $timeConditionBlockHint)
    {
        $this->timeConditionBlockHints->removeElement($timeConditionBlockHint);

        return $this;
    }
}
