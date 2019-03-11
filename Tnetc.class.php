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
use TelNowEdge\FreePBX\Base\Resources\Migrations\MigrationBuilder;
use TelNowEdge\Module\tnetc\Controller\AjaxController;
use TelNowEdge\Module\tnetc\Controller\DialplanController;
use TelNowEdge\Module\tnetc\Controller\FunctionController;
use TelNowEdge\Module\tnetc\Controller\PageController;
use TelNowEdge\Module\tnetc\Controller\TimeConditionController;
use TelNowEdge\Module\tnetc\Resources\Migrations\PhpMigration;
use TelNowEdge\Module\tnetc\Resources\Migrations\TimeConditionMigration;

class Tnetc extends Module implements \BMO
{
    public static function getMigrations()
    {
        return array(
            TimeConditionMigration::class,
            PhpMigration::class,
        );
    }

    public function install()
    {
        $builder = MigrationBuilder::createBuilder();

        foreach (self::getMigrations() as $x) {
            $builder->addMigration($this->get($x));
        }

        return $builder->install();
    }

    public function uninstall()
    {
        $builder = MigrationBuilder::createBuilder();

        foreach (self::getMigrations() as $x) {
            $builder->addMigration($this->get($x));
        }

        return $builder->uninstall();
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
        return $this
            ->get(AjaxController::class)
            ->search($query, $results)
            ;
    }

    public function ajaxRequest($req, &$setting)
    {
        switch ($req) {
        case 'getTimeConditions':
        case 'delete':
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
        case 'delete':
            return $this->get(AjaxController::class)->delete();
        }
    }

    public static function myDialplanHooks()
    {
        return true;
    }

    public function doDialplanHook(&$ext, $engine, $priority)
    {
        $this
            ->get(DialplanController::class)
            ->run($ext, $engine, $priority)
            ;
    }

    public function getActionBar()
    {
        $request = $this->get('request');

        if ('tnetc' !== $request->query->get('display')) {
            return array();
        }

        if (false === $request->query->has('action') && false === $request->query->has('id')) {
            return array();
        }

        $buttons = array(
            'reset' => array(
                'name' => 'reset',
                'id' => 'reset',
                'value' => _('Reset'),
            ),
            'submit' => array(
                'name' => 'submit',
                'id' => 'submit',
                'value' => _('Submit'),
            ),
        );

        if (true === $request->query->has('id')) {
            $buttons['delete'] = array(
                'name' => 'delete',
                'id' => 'delete',
                'value' => _('Delete'),
            );
            $buttons['duplicate'] = array(
                'name' => 'duplicate',
                'id' => 'duplicate',
                'value' => _('Duplicate'),
            );
        }

        return $buttons;
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

    /**
     * This function are called by FreePBX hook ; Please watch function.inc.php.
     */
    public function getDestinations()
    {
        return $this->get(FunctionController::class)->getDestinations();
    }

    public function getDestinationInfo($dest)
    {
        return $this->get(FunctionController::class)->getDestinationInfo($dest);
    }

    public function checkDestinations($dest)
    {
        return $this->get(FunctionController::class)->checkDestinations($dest);
    }
}
