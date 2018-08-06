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
use TelNowEdge\FreePBX\Base\Resources\Migrations\AbstractSqlMigration;

class TimeConditionMigration extends AbstractSqlMigration
{
    /**
     * @Migration()
     */
    public function migration2018080605()
    {
        return '
CREATE
    TABLE
        `tne_time_condition_block_hint` (
            `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
            ,`tne_time_condition_block_id` INT NOT NULL
            ,`type` VARCHAR(255)
            ,FOREIGN KEY (`tne_time_condition_block_id`) REFERENCES `tne_time_condition_block` (`id`)
        )
';
    }

    /**
     * @Migration()
     */
    public function migration2018080604()
    {
        return '
CREATE
    TABLE
        `tne_time_condition_block_calendar` (
            `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
            ,`policy` VARCHAR(255) NOT NULL
            ,`calendar_id` CHAR( 255 )
            ,`tne_time_condition_block_id` INT NOT NULL
            ,FOREIGN KEY (`tne_time_condition_block_id`) REFERENCES `tne_time_condition_block` (`id`)
        )
';
    }

    /**
     * @Migration()
     */
    public function migration2018080603()
    {
        return '
CREATE
    TABLE
        `tne_time_condition_block_tg` (
            `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
            ,`timegroups_groups_id` INT NOT NULL
            ,`tne_time_condition_block_id` INT NOT NULL
            ,FOREIGN KEY (`tne_time_condition_block_id`) REFERENCES `tne_time_condition_block` (`id`)
        )
';
    }

    /**
     * @Migration()
     */
    public function migration2018080602()
    {
        return '
CREATE
    TABLE
        `tne_time_condition_block` (
            `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
            ,`goto` VARCHAR (255) DEFAULT NULL
            ,`tne_time_condition_id` INT NOT NULL
            ,`weight` INT NOT NULL
            ,FOREIGN KEY (`tne_time_condition_id`) REFERENCES `tne_time_condition` (`id`)
        )
';
    }

    /**
     * @Migration()
     */
    public function migration2018080601()
    {
        return '
CREATE
    TABLE
        `tne_time_condition` (
            `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
            ,`name` VARCHAR (255) NOT NULL
            ,`internal_dial` INT DEFAULT NULL
            ,`daynight_id` VARCHAR (10) DEFAULT NULL
            ,`timezone` VARCHAR (255) NOT NULL
            ,`fallback` VARCHAR (255) DEFAULT NULL
        )
';
    }
}
