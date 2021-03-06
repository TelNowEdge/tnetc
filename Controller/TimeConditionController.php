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

use Doctrine\Common\Collections\ArrayCollection;
use TelNowEdge\FreePBX\Base\Controller\AbstractController;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\Module\tnetc\Form\TimeConditionType;
use TelNowEdge\Module\tnetc\Handler\DbHandler\TimeConditionBlockDbHandler;
use TelNowEdge\Module\tnetc\Handler\DbHandler\TimeConditionDbHandler;
use TelNowEdge\Module\tnetc\Model\TimeCondition;
use TelNowEdge\Module\tnetc\Repository\TimeConditionRepository;

class TimeConditionController extends AbstractController
{
    public function getRightNav()
    {
        return $this->render('right-nav.html.twig');
    }

    public function createAction()
    {
        $request = $this->get('request');

        $form = $this->createForm(
            TimeConditionType::class,
            new TimeCondition()
        );

        $form->handleRequest($request);

        if (true === $form->isValid() && true === $form->isSubmitted()) {
            needreload();

            $this->get(TimeConditionDbHandler::class)
                 ->create($form->getData())
                ;

            redirect(
                sprintf('config.php?display=tnetc&id=%d', $form->getData()->getId())
            );
        }

        return $this->processForm($form);
    }

    public function editAction($id)
    {
        $request = $this->get('request');

        try {
            $timeCondition = $this
                ->get(TimeConditionRepository::class)
                ->getById($id)
                ;
        } catch (NoResultException $e) {
            return;
        }

        $form = $this->createForm(
            TimeConditionType::class,
            $timeCondition
        );

        $form->handleRequest($request);

        if (true === $form->isValid()) {
            needreload();

            $this->get(TimeConditionDbHandler::class)
                 ->update($timeCondition)
                ;

            redirect(
                sprintf('config.php?display=tnetc&id=%d', $timeCondition->getId())
            );
        }

        $usedBy = framework_check_destination_usage(sprintf('time-condition-tne,%d,1', $timeCondition->getId()));

        return $this->processForm($form, $id, $usedBy);
    }

    public function duplicateAction()
    {
        $request = $this->get('request');

        $form = $this->createForm(
            TimeConditionType::class,
            $timeCondition
        );

        $form->handleRequest($request);

        if (true === $form->isValid()) {
            needreload();

            $timeCondition = $form->getData();

            try {
                $storedTimeConditions = $this
                    ->get(TimeConditionRepository::class)
                    ->getByNameLike($timeCondition->getName())
                    ;

                $names = array();
                foreach ($storedTimeConditions as $storedTimeCondition) {
                    array_push($names, $storedTimeCondition->getName());
                }

                rsort($names);

                $name = reset($names);

                if (0 !== preg_match('/(.+)_(.+)$/', $name, $match)) {
                    $timeCondition->setName(sprintf('%s_%d', $match[1], (int) $match[2] + 1));
                } else {
                    $timeCondition->setName(sprintf('%s_1', $name));
                }
            } catch (NoResultException $e) {
            }

            $this
                ->get(TimeConditionDbHandler::class)
                ->create($timeCondition)
                ;

            redirect(
                sprintf('config.php?display=tnetc&id=%d', $form->getData()->getId())
            );
        }

        return $this->processForm($form, $request->request->get('id', null));
    }

    public function deleteAction($id)
    {
        try {
            $timeCondition = $this
                ->get(TimeConditionRepository::class)
                ->getById($id)
                ;
        } catch (NoResultException $e) {
            return;
        }

        $this->get(TimeConditionDbHandler::class)
             ->delete($timeCondition)
            ;

        needreload();

        redirect('config.php?display=tnetc');
    }

    /**
     * Called  by Tnetc::class.
     */
    public function deleteTimeGroup($id)
    {
        try {
            $collection = $this
                ->get(TimeConditionRepository::class)
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            return $this;
        }

        $changed = new ArrayCollection();

        $collection->forAll(function ($null, $timeCondition) use ($changed, $id) {
            $timeCondition->getTimeConditionBlocks()->forAll(function ($null, $block) use ($changed, $id) {
                $block->getTimeConditionBlockTgs()->forAll(function ($null, $x) use ($block, $changed, $id) {
                    if ($x->getTimeGroup()->getId() !== $id) {
                        return true;
                    }

                    $block->removeTimeConditionBlockTg($x);

                    if (null === $changed->get($block->getId())) {
                        $changed->set($block->getId(), $block);
                    }

                    return true;
                });

                return true;
            });

            return true;
        });

        $changed->forAll(function ($k, $x) {
            $this
                ->get(TimeConditionBlockDbHandler::class)
                ->triggerUpdate($x)
                ;

            return true;
        });
    }

    /**
     * Called  by Tnetc::doConfigPageInit.
     */
    public function deleteCalendar()
    {
        $request = $this->get('request');

        $action = null !== $request->request->get('action')
            ? $request->request->get('action')
            : $request->query->get('action')
            ;

        $id = null !== $request->request->get('id')
            ? $request->request->get('id')
            : $request->query->get('id')
            ;

        if ('delete' !== $action) {
            return;
        }

        try {
            $collection = $this
                ->get(TimeConditionRepository::class)
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            return;
        }

        $changed = new ArrayCollection();

        $collection->forAll(function ($null, $timeCondition) use ($changed, $id) {
            $timeCondition->getTimeConditionBlocks()->forAll(function ($null, $block) use ($changed, $id) {
                $block->getTimeConditionBlockCalendars()->forAll(function ($null, $x) use ($block, $changed, $id) {
                    if ($x->getCalendar()->getId() !== $id) {
                        return true;
                    }

                    $block->removeTimeConditionBlockCalendar($x);

                    if (null === $changed->get($block->getId())) {
                        $changed->set($block->getId(), $block);
                    }

                    return true;
                });

                return true;
            });

            return true;
        });

        $changed->forAll(function ($k, $x) {
            $this
                ->get(TimeConditionBlockDbHandler::class)
                ->triggerUpdate($x)
                ;

            return true;
        });
    }

    public static function getViewsDir()
    {
        return sprintf('%s/../views', __DIR__);
    }

    public static function getViewsNamespace()
    {
        return 'tnetc';
    }

    private function processForm(\Symfony\Component\Form\Form $form, $id = null, $usedBy = null)
    {
        return $this->render('form.html.twig', array(
            'form' => $form->createView(),
            'id' => $id,
            'usedBy' => $usedBy,
        ));
    }
}
