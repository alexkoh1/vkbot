<?php
namespace AppBundle\Command;

use AppBundle\AppBundle;
use AppBundle\Entity\Attachment;
use AppBundle\Entity\Task;
use AppBundle\Entity\TaskLog;
use AppBundle\Entity\WallPost;
use AppBundle\Repository\TaskRepository;
use AppBundle\Service\PhotoService;
use AppBundle\Service\WallService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use getjump\Vk\Core;
use getjump\Vk\Model\Wall;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class Worker extends Command
{
    private $entityManager;

    private $wallService;

    private $taskRepository;

    public function __construct(
        EntityManager $entityManager,
        WallService $wallService,
        TaskRepository $taskRepository,
        $name = null)
    {
        parent::__construct($name);

        $this->entityManager  =  $entityManager;
        $this->wallService    =  $wallService;
        $this->taskRepository = $taskRepository;
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
            $lastVkId = $this->taskRepository->getVkIdOfLastPostedRecord($task);

            $this->wallService->createPost(
                $task->getToId(),
                $task->getFromId(),
                $lastVkId
            );

            $log = new TaskLog();
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
            return false;
        }
    }

    private function isInPause(Task $task)
    {
        $params = [
            'id' => $task->getId()
        ];
        $lastLog = $this->entityManager->getRepository(TaskLog::class)->findOneBy($params);
        if ($lastLog) {
            $lastTime = $lastLog->getTime();
        } else {
            $lastTime = new \DateTime();
        }
        $pauseFrom = new \DateInterval('PT'.($task->getPauseFrom()).'H');
        $pauseTo   = new \DateInterval('PT'.($task->getPauseTo()). 'H');
        $now = new \DateTime();

        $diff = $now->diff($lastTime);

        if ($pauseFrom >= $diff) {
            return true;
        } else {
            return false;
        }
    }
}