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

namespace TelNowEdge\Module\tnetc\Resources\Migrations;

use TelNowEdge\FreePBX\Base\Model\Annotation\Migration;
use TelNowEdge\FreePBX\Base\Resources\Migrations\AbstractPhpMigration;

class PhpMigration extends AbstractPhpMigration
{
    /**
     * @Migration(reinstall=true)
     */
    public function migration2018082001()
    {
        $freepbx_conf = \freepbx_conf::create();

        $set['defaultval'] = 'green';
        $set['value'] = $set['defaultval'];
        $set['readonly'] = 0;
        $set['hidden'] = 0;
        $set['level'] = 0;
        $set['module'] = 'tnetc';
        $set['category'] = 'TNE Settings';
        $set['emptyok'] = 0;
        $set['sortorder'] = 305;
        $set['name'] = 'Time condition default BLF state';
        $set['description'] = 'When a BLF is needed on time condition use this default state';
        $set['type'] = \CONF_TYPE_SELECT;
        $set['options'] = 'green,blink,red';
        $freepbx_conf->define_conf_setting('TNE_TC_DEFAULT_HINT', $set, true);
    }
}
