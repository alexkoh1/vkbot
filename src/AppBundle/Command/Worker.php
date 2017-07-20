<?php

declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\Entity\Task;
use AppBundle\Entity\TaskLog;
use AppBundle\Service\TaskService;
use AppBundle\Service\WallService;
use DateTime;
use Doctrine\ORM\EntityManager;
use getjump\Vk\Exception\Error;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Worker extends Command
{
    private $entityManager;

    private $wallService;

    private $taskService;

    private $logger;

    public function __construct(
        EntityManager $entityManager,
        WallService   $wallService,
        TaskService   $taskService,
        Logger        $logger,
        $name = null)
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->wallService   = $wallService;
        $this->taskService   = $taskService;
        $this->logger        = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('app:worker')
            ->setDescription('Main worker')
            ->setHelp('This command create a new post to wall.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentTasks = $this->getCurrentTasks();
        foreach ($currentTasks as $task) {

            $taskType = $task->getTaskType();

            if ($taskType->getType() === 'copy_wall') {

                $lastPostVkId = $this->taskService->getLastPostId($task);
                if ($lastPostVkId === 0) {
                    $this->taskService->setTimeStarted($task);
                    $now = new DateTime('now');
                    $this->logger->addInfo('Task #'.$task->getId().' was started at '.$now->format('Y-m-d H:i'));
                }

                $post = $this->taskService->getNextPost($lastPostVkId, $task);
                if ($post === null) {
                    $this->taskService->setTimeFinished($task);
                    $this->taskService->setStatus('finished', $task);
                    $now = new DateTime('now');
                    $this->logger->addInfo('Task #'.$task->getId().' was finished at '.$now->format('Y-m-d H:i'));
                    break;
                }

                 try {
                    $res = $this->wallService->createPost($task->getToId(), $post);
                } catch (Error $e) {
                    $this->logger->addInfo('Post #'.$post->getId().' creation failed. Error message: '.$e->getMessage());
                    break;
                }

                $this->taskService->addTaskLog($post, $task);
                $this->logger->addInfo('Wall post https://vk.com/wall'.$post->getFromId().'_'.$post->getVkId()
                    .' was copied to https://vk.com/wall'.$task->getToId().'_'.$res);
                break;
            }

            if ($taskType->getType() === 'parse_wall') {

                $fromId        = $task->getFromId();
                $recordsFromVk = $this->wallService->getPostsFromWall(40, 0, $fromId);

                foreach ($recordsFromVk as $record) {
                    $params = [
                        'vkId'    => $record->id,
                        'fromId' => $fromId,
                    ];
                    $post = $this->entityManager->getRepository('AppBundle\Entity\WallPost')->findBy($params);
                    if ($record->post_type == 'post' && !$post) {
                        $this->wallService->addRecordToDb($record);
                        //$this->logger->addInfo('Vk post');
                    }
                }
            }

        }
    }

    /**
     * @return Task[]
     */
    private function getCurrentTasks()
    {
        $params = [
            'status' => 'running',
        ];
        $tasks = $this->entityManager->getRepository(Task::class)->findBy($params);
        $currentTasks = [];
        foreach ($tasks as $task)
        {
            if ($this->isInWorkingTime($task) && $this->isInPause($task)) {
                $currentTasks [] = $task;
            }
        }

        return $currentTasks;

    }

    private function isInWorkingTime(Task $task)
    {
        $startWorkingTime = $task->getWorkingTimeFrom();
        $endWorkingTime   = $task->getWorkingTimeTo();

        $start_time = new \DateTime('today '.$startWorkingTime);
        $end_time   = new \DateTime('today '.$endWorkingTime);
        $now        = new \DateTime();

        if ($start_time <= $now && $now < $end_time) {
            return true;
        } else {
            return true;
        }
    }

    private function isInPause(Task $task)
    {
        $params = [
            'taskId' => $task->getId()
        ];
        $lastLog = $this->entityManager->getRepository(TaskLog::class)->findOneBy($params, ['id' => 'DESC']);
        if ($lastLog) {
            $lastTime = $lastLog->getTime();
        } else {
            $lastTime = new \DateTime();
        }
        $pauseFrom = new \DateInterval('PT'.($task->getPauseFrom()).'H');
        $pauseTo   = new \DateInterval('PT'.($task->getPauseTo()). 'H');
        $now = new \DateTime();

        $diff = $now->diff($lastTime);

        if ($diff >= $pauseFrom || $diff->s === 0) {
            return true;
        } else {
            return true;
        }
    }
}