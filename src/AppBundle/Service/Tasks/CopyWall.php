<?php

declare(strict_types = 1);

namespace AppBundle\Service\Tasks;

use AppBundle\Entity\Task;
use AppBundle\Entity\TaskType;
use AppBundle\Entity\WallPost;
use AppBundle\Repository\TaskRepository;
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
     * @var Logger
     */
    private $logger;

    /**
     * @var Task
     */
    private $task;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * CopyWall constructor.
     *
     * @param WallService $wallService
     * @param Logger      $logger
     * @param Task        $task
     */
    public function __construct(
        WallService $wallService,
        TaskRepository $taskRepository,
        Logger      $logger,
        Task $task
    ) {
        $this->task = $task;
        $this->wallService = $wallService;
        $this->logger      = $logger;
        $this->taskRepository = $taskRepository;
    }


    public function do()
    {
        $lastPostVkId = $this->getLastPostId();
        if ($lastPostVkId === 0) {
            $this->setTimeStarted();
            $now = new DateTime('now');
            $this->logger->addInfo('Task #'.$this->task->getId().' was started at '.$now->format('Y-m-d H:i'));
        }

        $post = $this->getNextPost($lastPostVkId);
        if ($post === null) {
            $this->setTimeFinished();
            $this->setStatus('finished');
            $now = new DateTime('now');
            $this->logger->addInfo('Task #'.$this->task->getId().' was finished at '.$now->format('Y-m-d H:i'));
            return;
        }

        try {
            $res = $this->wallService->createPost($this->task->getToId(), $post);
        } catch (Error $e) {
            $this->logger->addInfo('Post #'.$post->getId().' creation failed. Error message: '.$e->getMessage());
            $status = false;
            $this->addTaskLog($post, $status);
        }

        $status = true;
        $this->addTaskLog($post, $status);
        $this->logger->addInfo('Wall post https://vk.com/wall'.$post->getFromId().'_'.$post->getVkId()
            .' was copied to https://vk.com/wall'.$this->task->getToId().'_'.$res);
    }

}