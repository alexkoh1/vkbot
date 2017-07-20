<?php
declare(strict_types = 1);

namespace AppBundle\Service;


use AppBundle\Entity\Task;
use AppBundle\Entity\WallPost;
use AppBundle\Repository\TaskRepository;
use DateTime;

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

    /**
     * Получает текущие активные задания
     */
    public function getCurrentTask() {
        $currentTasks = $this->taskRepository->getCurrentTask();
        foreach ($currentTasks as $task) {
            if ($this->taskIsInWorkingTime($task) && $this->taskIsInPause($task)) {
                
            }
        }

    }

    /**
     * Проверяет, что текущее время рабочее для задания
     *
     * @param Task $task
     *
     * @return bool
     */
    private function taskIsInWorkingTime(Task $task)
    {
        $startWorkingTime = $task->getWorkingTimeFrom();
        $endWorkingTime   = $task->getWorkingTimeTo();

        $start_time = new DateTime('today '.$startWorkingTime);
        $end_time   = new DateTime('today '.$endWorkingTime);
        $now        = new DateTime();

        if ($start_time <= $now && $now < $end_time) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Проверяет, что с момента последнего выполнения задания прошло время, настроенное в параметрах
     *
     * @param Task $task
     * @return bool
     */
    private function taskIsInPause(Task $task)
    {
        $params = [
            'taskId' => $task->getId()
        ];

        $lastLog = $this->entityManager->getRepository(TaskLog::class)->findOneBy($params, ['id' => 'DESC']);

        if ($lastLog) {
            $lastTime = $lastLog->getTime();
        } else {
            $lastTime = new DateTime('now');
        }

        $pauseFrom =$task->getPauseFrom() * 3600;
        $now = new DateTime('now');

        $diff = $now->getTimestamp() - $lastTime->getTimestamp();

        if ($diff >= $pauseFrom || $diff < 2 ) {
            return true;
        } else {
            return false;
        }
    }

}