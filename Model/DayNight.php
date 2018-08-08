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

use TelNowEdge\FreePBX\Base\Form\Model\Destination;
use TelNowEdge\Module\musictne\Model\Recording;

class DayNight
{
    protected $ext;

    protected $fcDescription;

    protected $day;

    protected $night;

    protected $password;

    protected $nightRecording;

    protected $dayRecording;

    public function __construct()
    {
        $this->day = new Destination();
        $this->night = new Destination();
        $this->nightRecording = new Recording();
        $this->dayRecording = new Recording();
    }

    public function getExt()
    {
        return $this->ext;
    }

    public function setExt($ext)
    {
        $this->ext = $ext;

        return $this;
    }

    public function getFcDescription()
    {
        return $this->fcDescription;
    }

    public function setFcDescription($fcDescription)
    {
        $this->fcDescription = $fcDescription;

        return $this;
    }

    public function getDay()
    {
        return $this->day;
    }

    public function setDay(Destination $day)
    {
        $this->day = $day;

        return $this;
    }

    public function getNight()
    {
        return $this->night;
    }

    public function setNight(Destination $night)
    {
        $this->night = $night;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getNightRecording()
    {
        return $this->nightRecording;
    }

    public function setNightRecording(Recording $nightRecording)
    {
        $this->nightRecording = $nightRecording;

        return $this;
    }

    public function getDayRecording()
    {
        return $this->dayRecording;
    }

    public function setDayRecording(Recording $dayRecording)
    {
        $this->dayRecording = $dayRecording;

        return $this;
    }
}
