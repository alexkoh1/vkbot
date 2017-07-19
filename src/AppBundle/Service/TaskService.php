<?php
declare(strict_types = 1);

namespace AppBundle\Service;


use AppBundle\Entity\Task;
use AppBundle\Entity\WallPost;
use AppBundle\Repository\TaskRepository;

class TaskService
{

    private $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
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
    public function addTaskLog(WallPost $post, Task $task) {
        $this->taskRepository->addTaskLog($post, $task);
    }

    /**
     * Меняет статус задания
     */
    public function setStatus(string $status, Task $task)
    {
        $this->taskRepository->setStatus($status, $task);
    }
}