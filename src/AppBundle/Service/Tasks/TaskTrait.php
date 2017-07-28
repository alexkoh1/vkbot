<?php

declare(strict_types = 1);

namespace AppBundle\Service\Tasks;

use AppBundle\Entity\Task;
use AppBundle\Entity\WallPost;
use DateTime;

trait TaskTrait
{
    /**
     * Проверяет, что текущее время рабочее для задания
     *
     * @param Task $task
     *
     * @return bool
     */
    private function taskIsInWorkingTime()
    {
        $startWorkingTime = $this->task->getWorkingTimeFrom();
        $endWorkingTime   = $this->task->getWorkingTimeTo();

        if ($startWorkingTime === $endWorkingTime) {
            return true;
        }

        $start_time = new DateTime('today '.$startWorkingTime);
        $end_time   = new DateTime('today '.$endWorkingTime);
        $now        = new DateTime();

        if ($start_time <= $now && $now < $end_time) {
            return true;
        }

        return false;
    }

    /**
     * Проверяет, что с момента последнего выполнения задания прошло время, настроенное в параметрах
     *
     * @param Task $task
     * @return bool
     */
    private function taskIsInPause()
    {
        if ($this->task->getPauseFrom() === 0) {
            return true;
        }

        $lastLog = $this->getLastLogId();

        if ($lastLog) {
            $lastTime = $lastLog->getTime();
        } else {
            $lastTime = new DateTime('now');
        }

        $pauseFrom =$this->task->getPauseFrom() * 3600;
        $now = new DateTime('now');

        $diff = $now->getTimestamp() - $lastTime->getTimestamp();

        if ($diff >= $pauseFrom || $diff < 2 ) {
            return true;
        }

        return false;
    }

    /**
     * Проверяет должно ли выполняться задание в текущей итерации
     *
     * @return bool
     */
    public function isDoneble() {
        if ($this->taskIsInPause() && $this->taskIsInWorkingTime()) {
                return true;
        }
        return false;
    }

    /**
     * Устанавливает время начала выполнения задания
     */
    public function setTimeStarted() {
        $this->taskRepository->setTimeStarted($this->task);
    }

    /**
     * Устанавливает время окончания выполнения задания
     *
     * @param Task $task
     */
    public function setTimeFinished() {
        $this->taskRepository->setTimeFinished($this->task);
    }

    /**
     * Меняет статус задания
     */
    public function setStatus(string $status)
    {
        $this->taskRepository->setStatus($status, $this->task);
    }

    /**
     * Получает vkId последней опубликованной от имени определенного пользователя записи
     *
     * @param Task $task
     *
     * @return int
     */
    public function getLastPostId() {
        return $this->taskRepository->getLastPost($this->task);
    }

    /**
     * Получает id послденей опубликованной записи
     */
    public function getLastLogId() {
        return $this->taskRepository->getLastLogId($this->task);
    }

    /**
     * Получает запись для публикации
     *
     * @param $lastPostVkId
     * @param Task $task
     *
     * @return WallPost|null
     */
    public function getNextPost($lastPostVkId) {
        return $this->taskRepository->getNextPost($lastPostVkId, $this->task->getFromId());
    }

    /**
     * Добавляет новую запись в базу логов
     */
    public function addTaskLog(WallPost $post, bool $status) {
        $this->taskRepository->addTaskLog($post, $this->task, $status);
    }

    public function getTaskId() {
        return $this->task->getId();
    }

    public function getTaskType() {
        return $this->task->getTaskType()->getId();
    }

}