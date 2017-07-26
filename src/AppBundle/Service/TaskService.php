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
     * Получает vkId последней опубликованной от имени определенного пользователя записи
     *
     * @param Task $task
     *
     * @return int
     */
    public function getLastPostId(Task $task) {
        return $this->taskRepository->getLastPost($task);
    }

    /**
     * Получает id послденей опубликованной записи
     */
    public function getLastLogId(Task $task) {
        return $this->taskRepository->getLastLogId($task);
    }

    /**
     * Получает запись для публикации
     *
     * @param $lastPostVkId
     * @param Task $task
     *
     * @return WallPost|null
     */
    public function getNextPost($lastPostVkId, Task $task) {
        return $this->taskRepository->getNextPost($lastPostVkId, $task->getFromId());
    }

    /**
     * Устанавливает время начала выполнения задания
     *
     * @param Task $task
     */
    public function setTimeStarted(Task $task) {
        $this->taskRepository->setTimeStarted($task);
    }

    /**
     * Устанавливает время окончания выполнения задания
     *
     * @param Task $task
     */
    public function setTimeFinished(Task $task) {
        $this->taskRepository->setTimeFinished($task);
    }

    /**
     * Добавляет новую запись в базу логов
     */
    public function addTaskLog(WallPost $post, Task $task, bool $status) {
        $this->taskRepository->addTaskLog($post, $task, $status);
    }

    /**
     * Меняет статус задания
     */
    public function setStatus(string $status, Task $task)
    {
        $this->taskRepository->setStatus($status, $task);
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
            $taskCollection[] =  $this->doerFactory->createDoer($task, $this);
        }

        return $taskCollection;

        /*foreach ($currentTasks as $task) {

            $filterCurrentTasks[] = $this->doerFactory->createDoer($task);

            /*if ($this->taskIsInWorkingTime($task) && $this->taskIsInPause($task)) {
                if ($task->getTaskType() === 'copy_wall') {
                    $filterCurrentTasks[] = TaskDoerFactory::createDoer($task)
                    continue;
                }
                if ($task->getTaskType() === 'parse_wall') {
                    $filterCurrentTasks[] = new ParseWall($task);
                    continue;
                }
            }
        }*/

    }
}