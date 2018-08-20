#!/usr/bin/env php
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

use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;
use TelNowEdge\FreePBX\Base\Manager\DevStateManager;
use TelNowEdge\Module\tnetc\Repository\TimeConditionRepository;

$restrict_mods = array('tnetc' => true);
$bootstrap_settings['freepbx_auth'] = false;
include '/etc/freepbx.conf';

$container = FreePBX::tnehook()->getContainer();

$timeConditions = $container
    ->get(TimeConditionRepository::class)
    ->getCollection()
    ;

$defaultState = $container
    ->get(AmpConfManager::class)
    ->get('TNE_TC_DEFAULT_HINT')
    ;

$devStateManager = $container->get(DevStateManager::class);

foreach ($timeConditions as $timeCondition) {
    $fallback = true;

    foreach ($timeCondition->getTimeConditionBlocks() as $block) {
        if (true === $block->getTimeConditionBlockHints()->isEmpty()) {
            $state = convertHint($defaultState);
        } else {
            $state = convertHint($block->getTimeConditionBlockHints()->first()->getType());
        }

        $hint = sprintf('TCTNE%d', $timeCondition->getId());

        foreach ($block->getTimeConditionBlockTgs() as $tg) {
            foreach ($tg->getTimeGroup()->getTimes() as $time) {
                $match = FreePBX::timeconditions()->checkTime(sprintf(
                    '%s|%s',
                    $time,
                    $timeCondition->getTimezone()
                ));

                if (false === $match) {
                    continue;
                }

                $devStateManager->update($hint, $state);
                $fallback = false;
            }
        }

        foreach ($block->getTimeConditionBlockCalendars() as $calendar) {
            $timeMatch = FreePBX::Calendar()->matchCalendar($calendar->getCalendar()->getId());
            $next = FreePBX::Calendar()->getNextEvent($calendar->getCalendar()->getId(), $timeCondition->getTimezone());

            if (false === $timeMatch && 'inverse' === $calendar->getPolicy()) {
                $devStateManager->update($hint, $state);
                $fallback = false;
            }

            if (true === $timeMatch && 'straight' === $calendar->getPolicy()) {
                $devStateManager->update($hint, $state);
                $fallback = false;
            }
        }
    }

    if (false === $fallback) {
        continue;
    }

    $devStateManager->update($hint, convertHint($defaultState));
}

function convertHint($hint)
{
    switch ($hint) {
    case 'green':
        return 'NOT_INUSE';
    case 'red':
        return 'INUSE';
    case 'blink':
        return 'RINGING';
    }
}
