<?php

/*
 * This file is part of Hiject.
 *
 * Copyright (C) 2016 Hiject Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hiject\Bus\EventBuilder;

use Hiject\Bus\Event\GenericEvent;
use Hiject\Bus\Event\ProjectFileEvent;

/**
 * Class ProjectFileEventBuilder.
 */
class ProjectFileEventBuilder extends BaseEventBuilder
{
    protected $fileId = 0;

    /**
     * Set fileId.
     *
     * @param int $fileId
     *
     * @return $this
     */
    public function withFileId($fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * Build event data.
     *
     * @return GenericEvent|null
     */
    public function buildEvent()
    {
        $file = $this->projectFileModel->getById($this->fileId);

        if (empty($file)) {
            $this->logger->debug(__METHOD__.': File not found');

            return;
        }

        return new ProjectFileEvent([
            'file'    => $file,
            'project' => $this->projectModel->getById($file['project_id']),
        ]);
    }

    /**
     * Get event title with author.
     *
     * @param string $author
     * @param string $eventName
     * @param array  $eventData
     *
     * @return string
     */
    public function buildTitleWithAuthor($author, $eventName, array $eventData)
    {
        return '';
    }

    /**
     * Get event title without author.
     *
     * @param string $eventName
     * @param array  $eventData
     *
     * @return string
     */
    public function buildTitleWithoutAuthor($eventName, array $eventData)
    {
        return '';
    }
}
