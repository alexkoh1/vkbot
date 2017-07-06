<?php
namespace AppBundle\Command;

use AppBundle\AppBundle;
use AppBundle\Entity\Attachment;
use AppBundle\Entity\Task;
use AppBundle\Entity\WallPost;
use AppBundle\Service\PhotoService;
use AppBundle\Service\WallService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use getjump\Vk\Core;
use getjump\Vk\Model\Wall;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Worker extends Command
{
    private $entityManager;

    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
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
    }

    private function getCurrentTasks()
    {
        $params = [
            'status' => 'running',
        ];
        $currentTasks = $this->entityManager->getRepository(Task::class)->findBy($params);

        foreach ($currentTasks as $task)
        {
            //print_r($task);
            if ($this->inWorkingTime($task)) {
                print 'hui';
            }
        }

    }

    private function inWorkingTime(Task $task)
    {
        $startWorkingTime = $task->getWorkingTimeFrom();
        $endWorkingTime = $task->getWorkingTimeTo();
        $start_time = new \DateTime('today '.$startWorkingTime);
        $end_time = new \DateTime('today '.$endWorkingTime);
        $now = new \DateTime('+3 hours');

        if ($start_time <= $now && $now < $end_time) {
            return true;
        } else {
            return false;
        }
    }
}