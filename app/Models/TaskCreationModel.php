<?php

/*
 * This file is part of Hiject.
 *
 * Copyright (C) 2016 Hiject Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hiject\Model;

use Hiject\Core\Base;

/**
 * Task Creation.
 */
class TaskCreationModel extends Base
{
    /**
     * Create a task.
     *
     * @param array $values Form values
     *
     * @return int
     */
    public function create(array $values)
    {
        $position = empty($values['position']) ? 0 : $values['position'];
        $tags = [];

        if (isset($values['tags'])) {
            $tags = $values['tags'];
            unset($values['tags']);
        }

        $this->prepare($values);
        $task_id = $this->db->table(TaskModel::TABLE)->persist($values);

        if ($task_id !== false) {
            if ($position > 0 && $values['position'] > 1) {
                $this->taskPositionModel->movePosition($values['project_id'], $task_id, $values['column_id'], $position, $values['swimlane_id'], false);
            }

            if (!empty($tags)) {
                $this->taskTagModel->save($values['project_id'], $task_id, $tags);
            }

            $this->queueManager->push($this->taskEventJob->withParams(
                $task_id,
                [TaskModel::EVENT_CREATE_UPDATE, TaskModel::EVENT_CREATE]
            ));
        }

        return (int) $task_id;
    }

    /**
     * Prepare data.
     *
     * @param array $values Form values
     */
    protected function prepare(array &$values)
    {
        $values = $this->dateParser->convert($values, ['date_due']);
        $values = $this->dateParser->convert($values, ['date_started'], true);

        $this->helper->model->removeFields($values, ['another_task', 'duplicate_multiple_projects']);
        $this->helper->model->resetFields($values, ['creator_id', 'owner_id', 'swimlane_id', 'date_due', 'date_started', 'score', 'progress', 'category_id', 'time_estimated', 'time_spent']);

        if (empty($values['column_id'])) {
            $values['column_id'] = $this->columnModel->getFirstColumnId($values['project_id']);
        }

        if (empty($values['color_id'])) {
            $values['color_id'] = $this->colorModel->getDefaultColor();
        }

        if (empty($values['title'])) {
            $values['title'] = t('Untitled');
        }

        if ($this->userSession->isLogged()) {
            $values['creator_id'] = $this->userSession->getId();
        }

        $values['swimlane_id'] = empty($values['swimlane_id']) ? 0 : $values['swimlane_id'];
        $values['date_creation'] = time();
        $values['date_modification'] = $values['date_creation'];
        $values['date_moved'] = $values['date_creation'];
        $values['position'] = $this->taskFinderModel->countByColumnAndSwimlaneId($values['project_id'], $values['column_id'], $values['swimlane_id']) + 1;

        $this->hook->reference('model:task:creation:prepare', $values);
    }
}
