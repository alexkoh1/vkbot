<?php

declare(strict_types = 1);

namespace AppBundle\Service\Tasks;

use AppBundle\Entity\Task;
use AppBundle\Entity\TaskType;
use AppBundle\Service\TaskService;
use AppBundle\Service\WallService;
use DateTime;
use getjump\Vk\Exception\Error;
use Monolog\Logger;

class CopyWall implements TaskInterface
{
    use TaskTrait;

    /**
     * @var WallService
     */
    private $wallService;

    /**
     * @var TaskService
     */
    private $taskService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Task
     */
    private $task;

    /**
     * CopyWall constructor.
     *
     * @param WallService $wallService
     * @param TaskService $taskService
     * @param Logger      $logger
     * @param Task        $task
     */
    public function __construct(
        WallService $wallService,
        TaskService $taskService,
        Logger      $logger,
        Task $task
    ) {
        $this->task = $task;
        $this->wallService = $wallService;
        $this->taskService = $taskService;
        $this->logger      = $logger;
    }


    public function do()
    {
        $lastPostVkId = $this->taskService->getLastPostId($this->task);
        if ($lastPostVkId === 0) {
            $this->taskService->setTimeStarted($this->task);
            $now = new DateTime('now');
            $this->logger->addInfo('Task #'.$this->task->getId().' was started at '.$now->format('Y-m-d H:i'));
        }

        $post = $this->taskService->getNextPost($lastPostVkId, $this->task);
        if ($post === null) {
            $this->taskService->setTimeFinished($this->task);
            $this->taskService->setStatus('finished', $this->task);
            $now = new DateTime('now');
            $this->logger->addInfo('Task #'.$this->task->getId().' was finished at '.$now->format('Y-m-d H:i'));
            return;
        }

        try {
            $res = $this->wallService->createPost($this->task->getToId(), $post);
        } catch (Error $e) {
            $this->logger->addInfo('Post #'.$post->getId().' creation failed. Error message: '.$e->getMessage());
            $status = false;
            $this->taskService->addTaskLog($post, $this->task, $status);
        }

        $status = true;
        $this->taskService->addTaskLog($post, $this->task, $status);
        $this->logger->addInfo('Wall post https://vk.com/wall'.$post->getFromId().'_'.$post->getVkId()
            .' was copied to https://vk.com/wall'.$this->task->getToId().'_'.$res);
    }
}