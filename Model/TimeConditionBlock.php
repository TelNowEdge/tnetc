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

    public function __construct($child = false)
    {
        $this->timeConditionBlockTgs = new \Doctrine\Common\Collections\ArrayCollection(array(
            new TimeConditionBlockTg(true),
            new TimeConditionBlockTg(true),
            new TimeConditionBlockTg(true),
        ));

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
}
