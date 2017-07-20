<?php

declare(strict_types = 1);

use AppBundle\Entity\Task;

class ParseWall implements TaskInterface
{
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function do(Task $task)
    {
        // TODO: Implement do() method.
    }

}