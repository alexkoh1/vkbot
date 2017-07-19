<?php

declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\Entity\Task;
use AppBundle\Entity\TaskType;
use AppBundle\Entity\WallPost;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class CreateTask extends Command
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
            ->setName('app:create-task')
            ->setDescription('Main worker')
            ->setHelp('This command create a new post to wall.');
        /*    ->addArgument('task_type', InputArgument::REQUIRED, 'Task type: parse_wall, copy_wall')
            ->addArgument('from_id', InputArgument::REQUIRED, 'Source Vk page ')
            ->addArgument('to_id', InputArgument::OPTIONAL , 'Destination Vk page ')
            ->addArgument('working_time', InputArgument::REQUIRED, 'Working time, example: 00:00-18:00')
            ->addArgument('pause_from', InputArgument::REQUIRED, 'Pause from, in hours: 3')*/
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /*$workingTime = explode('-', $input->getArgument('working_time'));

        $task = new Task();

        $task->setFromId($input->getArgument('from_id'));
        $task->setTaskType($this->getTaskType($input->getArgument('task_type')));
        $task->setToId($input->getArgument('to_id'));
        $task->setWorkingTimeFrom($workingTime[0]);
        $task->setWorkingTimeTo($workingTime[1]);
        $task->setPauseFrom($input->getArgument('pause_from'));*/

        $helper = $this->getHelper('question');
        var_dump($this->getFromId());

        $question = new ChoiceQuestion('Введите id страницы-источника: ', $this->getFromId());
        $fromId = $helper->ask($input, $output, $question);

        $question = new Question('Введите id страницы-назначения: ');
        $toId = $helper->ask($input, $output, $question);

        $question = new ChoiceQuestion('Выберите тип задания: ',
            array('copy_wall', 'parse_wall'),
            0
        );
        $question->setErrorMessage('Type %s is invalid');
        $taskType = $helper->ask($input, $output, $question);

        $question = new Question('Введите часы, в которые задача должна выполняться: ', '10:00-18:00');
        $workingTime = $helper->ask($input, $output, $question);

        $question = new Question('Введите паузу: ', '4');
        $pause = $helper->ask($input, $output, $question);


        $workingTime = explode('-', $workingTime);
        $task = new Task();
        $task->setFromId($fromId);
        $task->setTaskType($this->getTaskType($taskType));
        $task->setToId($toId);
        $task->setWorkingTimeFrom($workingTime[0]);
        $task->setWorkingTimeTo($workingTime[1]);
        $task->setPauseFrom($pause);
        $task->setPauseTo(0);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

    }

    private function getTaskType($description)
    {
        return $this->entityManager->getRepository(TaskType::class)->findOneByType($description);
    }

    private function getFromId()
    {

        $result = $this->entityManager->getRepository(WallPost::class)
            ->createQueryBuilder('u')
            ->select('u.fromId')
            ->groupBy('u.fromId')
            ->getQuery()
            ->getResult();

        $fromIdArray = [];
        foreach ($result as $data) {
            $fromIdArray[] = $data['fromId'];
        }

        return $fromIdArray;
    }
}