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

namespace TelNowEdge\Module\tnetc\DialPlan\Generator;

use TelNowEdge\FreePBX\Base\DialPlan\Generator\AbstractGenerator;
use TelNowEdge\FreePBX\Base\DialPlan\Verb;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;
use TelNowEdge\Module\tnetc\Model\TimeCondition;
use TelNowEdge\Module\tnetc\Model\TimeConditionBlock;
use TelNowEdge\Module\tnetc\Repository\TimeConditionRepository;

class TimeConditionGenerator extends AbstractGenerator
{
    public function generate(&$ext, $engine, $priority)
    {
        if ('asterisk' !== $engine) {
            return;
        }

        try {
            $timeConditions = $this->container
                ->get(TimeConditionRepository::class)
                ->getCollection()
                ;
        } catch (NoResultException $e) {
        }

        $context = 'time-condition-tne';

        $ext->addInclude('from-internal-additional', $context);

        foreach ($timeConditions as $timeCondition) {
            $this
                ->processHint($timeCondition, $ext, $context)
                ->processInternalDial($timeCondition, $ext, $context)
                ->processDayNight($timeCondition, $ext, $context)
                ;

            $ext->add(
                $context,
                $timeCondition->getId(),
                false,
                new Verb\Verbose(2, sprintf('Tme time condition: %d', $timeCondition->getId()))
            );

            foreach ($timeCondition->getTimeConditionBlocks() as $block) {
                $this
                    ->processStates($timeCondition, $block, $ext, $context)
                    ->processTimeGroups($timeCondition, $block, $ext, $context)
                    ->processCalendars($timeCondition, $block, $ext, $context)
                    ;
            }

            $this->processFallback($timeCondition, $ext, $context);

            $ext->add(
                $context,
                'h',
                false,
                new \ext_goto(1, 'hangup', 'app-blackhole')
            );
        }
    }

    private function processHint(Timecondition $timeCondition, $ext, $tcContext)
    {
        $context = 'tne-hints';
        $ext->addInclude('from-internal-additional', $context);

        $ext->add(
            $context,
            sprintf('*27%d', $timeCondition->getId()),
            false,
            new \ext_noop(sprintf(
                '[time-condition-tne] %d',
                $timeCondition->getId()
            ))
        );

        $ext->addHint(
            'tne-hints',
            sprintf('*27%d', $timeCondition->getId()),
            sprintf('Custom:TCTNE%d', $timeCondition->getId())
        );

        return $this;
    }

    private function processInternalDial(Timecondition $timeCondition, $ext, $tcContext)
    {
        $context = 'time-condition-tne-dial';

        if (null === $timeCondition->getInternalDial()) {
            return $this;
        }

        $ext->addInclude('from-internal-additional', $context);

        $ext->add(
            $context,
            $timeCondition->getInternalDial(),
            false,
            new Verb\Verbose(2, sprintf(
                '[TimeCondition][%d][Direct Dial] %s',
                $timeCondition->getId(),
                $timeCondition->getName()
            ))
        );

        $ext->add(
            $context,
            $timeCondition->getInternalDial(),
            false,
            new \ext_goto(1, $timeCondition->getId(), $tcContext)
        );

        return $this;
    }

    private function processDayNight(Timecondition $timeCondition, $ext, $tcContext)
    {
        if (null === $timeCondition->getDayNight()->getExt()) {
            return $this;
        }

        $ext->add(
            $tcContext,
            $timeCondition->getId(),
            false,
            new \ext_gotoif(sprintf(
                '$["${DB(DAYNIGHT/C%d)}" != "DAY"]',
                $timeCondition->getDayNight()->getExt()
            ), 'fallback')
        );

        return $this;
    }

    private function processTimeGroups(Timecondition $timeCondition, TimeConditionBlock $block, $ext, $context)
    {
        foreach ($block->getTimeConditionBlockTgs() as $timeGroup) {
            $ext->add(
                $context,
                $timeCondition->getId(),
                false,
                new Verb\Verbose(2, sprintf(
                    'TIMENOW: ${STRFTIME(${EPOCH},%1$s,%%H:%%M,%%a,%%e,%%b)}, %1$s',
                    $timeCondition->getTimezone()
                ))
            );

            foreach ($timeGroup->getTimeGroup()->getTimes() as $time) {
                $ext->add(
                    $context,
                    $timeCondition->getId(),
                    false,
                    new Verb\Verbose(2, sprintf(
                        'TIMEMATCHED: ${IFTIME(%s?TRUE:FALSE)}',
                        preg_replace('/\|/', ',', $time)
                    ))
                );

                $ext->add(
                    $context,
                    $timeCondition->getId(),
                    false,
                    new \ext_gotoiftime(sprintf(
                        '%s,%s',
                        $time,
                        $timeCondition->getTimezone()
                    ), $block->getGoto()->getDestination())
                );
            }
        }

        return $this;
    }

    private function processCalendars(Timecondition $timeCondition, TimeConditionBlock $block, $ext, $context)
    {
        foreach ($block->getTimeConditionBlockCalendars() as $calendar) {
            try {
                $goto = \FreePBX::Calendar()
                    ->ext_calendar_goto(
                        $calendar->getCalendar()->getId(),
                        $calendar->getCalendar()->getTimezone(),
                        'straight' === $calendar->getPolicy() ? $block->getGoto()->getDestination() : null,
                        'inverse' === $calendar->getPolicy() ? $block->getGoto()->getDestination() : null
                    );

                $ext->add(
                    $context,
                    $timeCondition->getId(),
                    false,
                    $goto
                );
            } catch (Exception $e) {
                $uid = 'CAL-'.$item['calendar_group_id'];
                if (!$nt->exists($rawname, $uid)) {
                    $nt->add_notice($rawname, $uid, _('Calendar Not found'), _('Your timecondition is linked to a non-existant calendar'), '?display=timeconditions&view=form&itemid='.$item['timeconditions_id'], true, false);
                }
                dbug($e->getMessage());
            }
        }

        return $this;
    }

    private function processStates(Timecondition $timeCondition, TimeConditionBlock $block, $ext, $context)
    {
        if (true === $block->getTimeConditionBlockHints()->isEmpty()) {
            return $this;
        }

        $devState = $this->container
            ->get(AmpConfManager::class)
            ->get('AST_FUNC_DEVICE_STATE')
            ;

        foreach ($block->getTimeConditionBlockHints() as $hint) {
            switch ($hint->getType()) {
            case 'green':
                $state = 'NOT_INUSE';
                break;
            case 'red':
                $state = 'INUSE';
                break;
            case 'blink':
                $state = 'RINGING';
                break;
            }

            $ext->add(
                $context,
                $timeCondition->getId(),
                false,
                new \ext_set(sprintf(
                    '%s(Custom:TCTNE%d)',
                    $devState,
                    $timeCondition->getId()
                ), $state)
            );
        }

        return $this;
    }

    private function processFallback(Timecondition $timeCondition, $ext, $context)
    {
        $ext->add(
            $context,
            $timeCondition->getId(),
            'fallback',
            new \ext_goto(
                $timeCondition->getFallback()->getDestination()
            )
        );
    }
}
