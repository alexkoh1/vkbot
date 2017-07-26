<?php

declare(strict_types = 1);

namespace AppBundle\Service\Tasks;

use AppBundle\Entity\Task;

class ParseWall implements TaskInterface
{
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function do()
    {
        $fromId        = $this->task->getFromId();
        $recordsFromVk = $this->wallService->getPostsFromWall(40, 0, $fromId);

        foreach ($recordsFromVk as $record) {
            $params = [
                'vkId'    => $record->id,
                'fromId' => $fromId,
            ];
            $post = $this->entityManager->getRepository('AppBundle\Entity\WallPost')->findBy($params);
            if ($record->post_type == 'post' && !$post && !empty($record->message)) {
                $this->wallService->addRecordToDb($record);
                //$this->logger->addInfo('Vk post');
            }
        }
    }

}