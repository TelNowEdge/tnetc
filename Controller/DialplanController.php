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
        foreach (\FreePBX::Cron()->getAll() as $cron) {
            if (preg_match('/scheduleTimeCondition.php/', $cron)) {
                \FreePBX::Cron()->remove($cron);
            }
        }

        $cronEnabled = $this
            ->get(AmpConfManager::class)
            ->get('TCMAINT')
            ;

        if (0 === (int) $cronEnabled) {
            return;
        }

        $interval = $this
            ->get(AmpConfManager::class)
            ->get('TCINTERVAL')
            ;

        switch ($interval) {
        case '60':
            $timing = '* * * * *';
            break;
        case '120':
            $timing = '*/2 * * * *';
            break;
        case '180':
            $timing = '*/3 * * * *';
            break;
        case '240':
            $timing = '*/4 * * * *';
            break;
        case '300':
            $timing = '*/5 * * * *';
            break;
        case '600':
            $timing = '*/10 * * * *';
            break;
        case '900':
            $timing = '*/15 * * * *';
            break;
        default:
            $timing = '* * * * *';
            break;
        }

        $libDir = $this
            ->get(AmpConfManager::class)
            ->get('ASTVARLIBDIR')
            ;

        $line = sprintf(
            '%1$s [ -x %2$s/bin/scheduleTimeCondition.php ] && %2$s/bin/scheduleTimeCondition.php',
            $timing,
            $libDir
        );

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
