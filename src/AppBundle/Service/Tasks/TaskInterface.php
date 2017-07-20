<?php

declare(strict_types = 1);

use AppBundle\Entity\Task;

interface TaskInterface
{
    public function do(Task $task);
}