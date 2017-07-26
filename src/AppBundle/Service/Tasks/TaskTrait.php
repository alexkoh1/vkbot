<?php

declare(strict_types = 1);

namespace AppBundle\Service\Tasks;

use AppBundle\Entity\Task;
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
        $lastLog = $this->taskService->getLastLogId($task);

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

    public function isDoneble() {
        if ($this->taskIsInPause($this->task) && $this->taskIsInWorkingTime($this->task)) {
                return true;
        }
        return false;
    }

}