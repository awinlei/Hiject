<?php

/*
 * This file is part of Hiject.
 *
 * Copyright (C) 2016 Hiject Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Hiject\Bus\EventBuilder\TaskLinkEventBuilder;
use Hiject\Model\ProjectModel;
use Hiject\Model\TaskCreationModel;
use Hiject\Model\TaskLinkModel;

require_once __DIR__.'/../Base.php';

class TaskLinkEventBuilderTest extends Base
{
    public function testWithMissingLink()
    {
        $taskLinkEventBuilder = new TaskLinkEventBuilder($this->container);
        $taskLinkEventBuilder->withTaskLinkId(42);
        $this->assertNull($taskLinkEventBuilder->buildEvent());
    }

    public function testBuild()
    {
        $taskLinkModel = new TaskLinkModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskLinkEventBuilder = new TaskLinkEventBuilder($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskCreationModel->create(['title' => 'task 1', 'project_id' => 1]));
        $this->assertEquals(2, $taskCreationModel->create(['title' => 'task 2', 'project_id' => 1]));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 1));

        $event = $taskLinkEventBuilder->withTaskLinkId(1)->buildEvent();

        $this->assertInstanceOf('Hiject\Bus\Event\TaskLinkEvent', $event);
        $this->assertNotEmpty($event['task_link']);
        $this->assertNotEmpty($event['task']);
    }

    public function testBuildTitle()
    {
        $taskLinkModel = new TaskLinkModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskLinkEventBuilder = new TaskLinkEventBuilder($this->container);

        $this->assertEquals(1, $projectModel->create(['name' => 'test1']));
        $this->assertEquals(1, $taskCreationModel->create(['title' => 'task 1', 'project_id' => 1]));
        $this->assertEquals(2, $taskCreationModel->create(['title' => 'task 2', 'project_id' => 1]));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 1));

        $eventData = $taskLinkEventBuilder->withTaskLinkId(1)->buildEvent();

        $title = $taskLinkEventBuilder->buildTitleWithAuthor('Foobar', TaskLinkModel::EVENT_CREATE_UPDATE, $eventData->getAll());
        $this->assertEquals('Foobar set a new internal link for the task #1', $title);

        $title = $taskLinkEventBuilder->buildTitleWithAuthor('Foobar', TaskLinkModel::EVENT_DELETE, $eventData->getAll());
        $this->assertEquals('Foobar removed an internal link for the task #1', $title);

        $title = $taskLinkEventBuilder->buildTitleWithAuthor('Foobar', 'not found', $eventData->getAll());
        $this->assertSame('', $title);

        $title = $taskLinkEventBuilder->buildTitleWithoutAuthor(TaskLinkModel::EVENT_CREATE_UPDATE, $eventData->getAll());
        $this->assertEquals('A new internal link for the task #1 have been defined', $title);

        $title = $taskLinkEventBuilder->buildTitleWithoutAuthor(TaskLinkModel::EVENT_DELETE, $eventData->getAll());
        $this->assertEquals('Internal link removed for the task #1', $title);

        $title = $taskLinkEventBuilder->buildTitleWithoutAuthor('not found', $eventData->getAll());
        $this->assertSame('', $title);
    }
}
