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

namespace FreePBX\modules;

use TelNowEdge\FreePBX\Base\Module\Module;
use TelNowEdge\Module\tnetc\Controller\AjaxController;
use TelNowEdge\Module\tnetc\Controller\PageController;
use TelNowEdge\Module\tnetc\Controller\TimeConditionController;
use TelNowEdge\Module\tnetc\Resources\Migrations\TimeConditionMigration;

class Tnetc extends Module implements \BMO
{
    public function install()
    {
        $this
            ->get(TimeConditionMigration::class)
            ->migrate()
            ;
    }

    public function uninstall()
    {
    }

    public function backup()
    {
    }

    public function restore($backup)
    {
    }

    public function doConfigPageInit($page)
    {
    }

    public function search($query, &$results)
    {
        /* return $this->get(AjaxController::class) */
        /*      ->search($query, $results) */
        /*     ; */
    }

    public function ajaxRequest($req, &$setting)
    {
        switch ($req) {
        case 'getTimeConditions':
            return true;
        }

        return false;
    }

    public function ajaxHandler()
    {
        $request = $this->get('request');
        $command = $request->query->get('command') ?: $request->request->get('command');

        switch ($command) {
        case 'getTimeConditions':
            return $this->get(AjaxController::class)->getTimeConditions();
        }
    }

    public function getRightNav()
    {
        $request = $this->get('request');

        if ('tnetc' !== $request->query->get('display') || (false === $request->query->has('id') && 'add' !== $request->query->get('action'))) {
            return;
        }

        return $this
            ->get(TimeConditionController::class)
            ->getRightNav()
            ;
    }

    public function pageInit()
    {
        $request = $this->get('request');

        if ('tnetc' !== $request->query->get('display')) {
            return;
        }

        if ('true' === $request->request->get('duplicate')) {
            return $this
                ->get(TimeConditionController::class)
                ->duplicateAction()
                ;
        }

        if (0 < $id = $request->query->getInt('id', 0)) {
            if ('del' === $request->query->get('action')) {
                return $this
                    ->get(TimeConditionController::class)
                    ->deleteAction($id)
                    ;
            }

            return $this
                ->get(TimeConditionController::class)
                ->editAction($id)
                ;
        }

        if ('add' === $request->query->get('action')) {
            return $this
                ->get(TimeConditionController::class)
                ->createAction()
                ;
        }

        return $this
            ->get(PageController::class)
            ->showAction()
            ;
    }
}
