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

    /**
     *Сервис задач
     *
     * @var TaskService
     */
    private $taskService;

    private $logger;

    public function __construct(
        EntityManager $entityManager,
        WallService   $wallService,
        TaskService   $taskService,
        Logger        $logger,
        $name = null
    ) {
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
        $currentTasks = $this->taskService->getCurrentTasks();
        $this->logger->addInfo('Starting new iteration');
        foreach ($currentTasks as $task) {
            if ($task->isDoneble() ) {
                $this->logger->addInfo('Task #'.$task->getTaskId().' started.');
                $task->do();
                $this->logger->addInfo('Task #'.$task->getTaskId().' ended.');
            }
        }
        $this->logger->addInfo('Iteration ended.');
    }
}