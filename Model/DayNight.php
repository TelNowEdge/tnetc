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

class DayNight
{
    protected $ext;

    protected $fcDescription;

    protected $day;

    protected $night;

    protected $password;

    /**
     * To keep tnetc standalone doesn't perform the join with musictne/Recording.
     */
    protected $nightRecordingId;

    protected $dayRecordingId;

    public function __construct()
    {
        $this->day = new Destination();
        $this->night = new Destination();
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

    public function getNightRecordingId()
    {
        return $this->nightRecordingId;
    }

    public function setNightRecordingId($nightRecordingId)
    {
        $this->nightRecordingId = $nightRecordingId;

        return $this;
    }

    public function getDayRecordingId()
    {
        return $this->dayRecordingId;
    }

    public function setDayRecordingId($dayRecordingId)
    {
        $this->dayRecordingId = $dayRecordingId;

        return $this;
    }
}
