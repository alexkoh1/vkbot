<?php

declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\Entity\Bot;
use AppBundle\Entity\Task;
use AppBundle\Entity\TaskType;
use AppBundle\Entity\WallPost;
use AppBundle\Service\UserService;
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

    private $userService;

    public function __construct(EntityManager $entityManager, UserService $userService, $name = null)
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->userService   = $userService;
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
        $helper = $this->getHelper('question');

        $question = new Question('Введите id страницы-источника:');
        print_r($this->getFromId());
        $fromId = $helper->ask($input, $output, $question);//explode(' ', $helper->ask($input, $output, $question))[0];

        $question = new Question('Введите id страницы-назначения: ');
        print_r($this->getToId());
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
        $task->setStatus('running');

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

        $userList = [];
        foreach ($fromIdArray as $fromId) {
            $userInfo = $this->userService->getUserFio($fromId);
            $userList[] = $fromId.' https://vk.com/id'.$fromId.' '.$userInfo[0]->first_name.' '.$userInfo[0]->last_name;
        }

        return $userList;
    }

    private function getToId()
    {
        $result = $this->entityManager->getRepository(Bot::class)
            ->createQueryBuilder('u')
            ->select('u.vkId')
            ->getQuery()
            ->getResult();

        $fromIdArray = [];
        foreach ($result as $data) {
            $fromIdArray[] = $data['vkId'];
        }

        $userList = [];
        foreach ($fromIdArray as $fromId) {
            $userInfo = $this->userService->getUserFio((int) $fromId);
            $userList[] = $fromId.' https://vk.com/id'.$fromId.' '.$userInfo[0]->first_name.' '.$userInfo[0]->last_name;
        }

        return $userList;
    }
}