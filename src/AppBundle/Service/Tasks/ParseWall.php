<?php

declare(strict_types = 1);

namespace AppBundle\Service\Tasks;

use AppBundle\Entity\Task;
use AppBundle\Repository\TaskRepository;
use AppBundle\Service\WallService;
use getjump\Vk\Model\Wall;
use Monolog\Logger;

class ParseWall implements TaskInterface
{
    use TaskTrait;

    private $task;

    private $wallService;

    private $logger;

    private $taskRepository;

    public function __construct(
        Task $task,
        WallService $wallService,
        TaskRepository $taskRepository,
        Logger $logger
    ) {
        $this->task        = $task;
        $this->wallService = $wallService;
        $this->logger      = $logger;
        $this->taskRepository = $taskRepository;
    }

    public function do()
    {
        $this->setTimeStarted();
        $fromId        = $this->task->getFromId();
        $recordsFromVk = $this->wallService->getPostsFromWall(80, 0, $fromId);
        $this->setTimeStarted();
        $count = 0;
        foreach ($recordsFromVk as $record) {
            $params = [
                'vkId'    => $record->id,
                'fromId' => $fromId,
            ];
            $post = $this->wallService->getPostByParams($params);

            if ($record->post_type === 'post' && !$post && !empty($record->text)) {
                $this->wallService->addRecordToDb($record);
                $count++;
                //$this->logger->addInfo('Vk post');
            }
        }
        $this->setStatus('finished');
        $this->setTimeFinished();
        $this->logger->addInfo($count.' posts was copied from https://vk.com/id'.$fromId.'.');
    }
}