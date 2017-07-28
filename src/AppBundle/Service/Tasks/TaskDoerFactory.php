<?php

declare(strict_types = 1);

namespace AppBundle\Service\Tasks;

use AppBundle\Entity\Task;
use AppBundle\Repository\TaskRepository;
use AppBundle\Service\TaskService;
use AppBundle\Service\WallService;
use Monolog\Logger;
use Symfony\Component\Form\Tests\CompoundFormPerformanceTest;

class TaskDoerFactory
{

    private $wallService;

    private $logger;

    public function __construct(
        WallService $wallService,
        Logger      $logger
    )
    {
        $this->wallService = $wallService;
        $this->logger      = $logger;
    }


    public function createDoer(Task $task, TaskRepository $taskRepository) {
        if ($task->getTaskType()->getType() === 'copy_wall') {
            return new CopyWall($this->wallService, $taskRepository, $this->logger, $task);
        }
        if ($task->getTaskType()->getType() === 'parse_wall') {
            return new ParseWall($task, $this->wallService, $taskRepository, $this->logger);
        }

        return 'hui';
    }
}