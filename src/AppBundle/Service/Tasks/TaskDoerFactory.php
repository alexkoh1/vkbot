<?php

declare(strict_types = 1);

namespace AppBundle\Service\Tasks;

use AppBundle\Entity\Task;
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


    public function createDoer(Task $task, TaskService $taskService) {
        if ($task->getTaskType()->getType() === 'copy_wall') {
            return new CopyWall($this->wallService, $taskService, $this->logger, $task);
        }
            return 'hui';
    }
}