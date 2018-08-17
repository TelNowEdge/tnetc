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

namespace TelNowEdge\Module\tnetc\Controller;

use TelNowEdge\FreePBX\Base\Controller\AbstractController;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;
use TelNowEdge\Module\tnetc\DialPlan\Generator;

class DialplanController extends AbstractController
{
    public function run(&$ext, $engine, $priority)
    {
        $this->get(Generator\TimeConditionGenerator::class)->generate($ext, $engine, $priority);
        $this->addCron();
    }

    public function addCron()
    {
        $libDir = $this
            ->get(AmpConfManager::class)
            ->get('ASTVARLIBDIR')
            ;

        foreach (\FreePBX::Cron()->getAll() as $cron) {
            if (preg_match('/scheduleTimeCondition.php/', $cron)) {
                \FreePBX::Cron()->remove($cron);
            }
        }

        $line = sprintf(
            '* * * * * [ -x %1$s/bin/scheduleTimeCondition.php ] && %1$s/bin/scheduleTimeCondition.php',
            $libDir
        );
        xdebug_break();
        \FreePBX::Cron()->add($line);
    }

    public static function getViewsDir()
    {
        return sprintf('%s/../views', __DIR__);
    }

    public static function getViewsNamespace()
    {
        return 'tnetc';
    }
}
