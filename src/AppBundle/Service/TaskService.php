<?php
declare(strict_types = 1);

namespace AppBundle\Service;


use AppBundle\Entity\Task;
use AppBundle\Entity\TaskLog;
use AppBundle\Entity\WallPost;
use AppBundle\Repository\TaskRepository;
use AppBundle\Service\Tasks\CopyWall;
use AppBundle\Service\Tasks\ParseWall;
use AppBundle\Service\Tasks\TaskDoerFactory;
use DateTime;

class TaskService
{

    private $taskRepository;

    private $doerFactory;

    public function __construct(
        TaskRepository $taskRepository,
        TaskDoerFactory $doerFactory
    )
    {
        $this->taskRepository = $taskRepository;
        $this->doerFactory = $doerFactory;
    }


    /**
     * Получает текущие активные задания
     *
     * return TaskInterface[]
     */
    public function getCurrentTasks(): array
    {
        $currentTasks = $this->taskRepository->getCurrentTasks();

        $taskCollection = [];

        foreach ($currentTasks as $task) {
            $taskCollection[] =  $this->doerFactory->createDoer($task, $this->taskRepository);
        }

        return $taskCollection;
    }
}