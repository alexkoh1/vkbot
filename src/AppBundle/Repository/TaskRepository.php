<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Task;
use AppBundle\Entity\TaskLog;
use AppBundle\Entity\WallPost;
use DateTime;
use Doctrine\ORM\EntityManager;
use getjump\Vk\Model\Wall;

class TaskRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Получает vkId последней опубликованной от имени определенного пользователя записи
     *
     * @param Task $task
     *
     * @return int
     */
    public function getLastPost(Task $task) {

        $entity = $this->entityManager
            ->getRepository('AppBundle:TaskLog')
            ->createQueryBuilder('e')
            ->join('e.recordId', 'r')
            ->where('e.taskId = '.$task->getId())
            ->select('r.vkId')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($entity) {
            $lastPostVkId = $entity['vkId'];
        } else {
            $lastPostVkId = 0;
        }

        return $lastPostVkId;
    }

    /**
     * Получает запись, следующую после vkId
     *
     * @param int $vkId
     * @param int $fromId
     *
     * @return WallPost|null
     */
    public function getNextPost(int $vkId, int $fromId)
    {
        $post = $this->entityManager
            ->getRepository('AppBundle:WallPost')
            ->createQueryBuilder('e')
            ->where('e.fromId = '.$fromId)
            ->andWhere('e.vkId > '.$vkId)
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $post;
    }

    /**
     * Устанавливает время начала выполнения задания
     *
     * @param Task $task
     */
    public function setTimeStarted(Task $task) {
        $task->setTimeStarted(new \DateTime('now'));
        $this->entityManager->flush();
    }

    /**
     * Устанавливает время окончания выполнения задания
     *
     * @param Task $task
     */
    public function setTimeFinished(Task $task) {
        $task->setTimeFinished(new \DateTime('now'));
        $this->entityManager->flush();
    }

    /**
     * Добавляет запись в базу логов
     *
     * @param WallPost $post
     * @param Task $task
     * @param bool $status
     */
    public function addTaskLog(WallPost $post, Task $task, bool $status)
    {
        $taskLog = new TaskLog();

        $taskLog->setRecordId($post);
        $taskLog->setStatus($status);
        $taskLog->setTaskId($task);
        $taskLog->setTime(new DateTime());

        $this->entityManager->persist($taskLog);
        $this->entityManager->flush();
    }

    /**
     * Обновляет статус задания
     *
     * @param string $status
     * @param Task $task
     */
    public function setStatus(string $status, Task $task)
    {
        $task->setStatus($status);
        $this->entityManager->flush();
    }

    /**
     * Получает текущие активные задания
     *
     * @return Task[]
     */
    public function getCurrentTask()
    {
        $params = [
            'status' => 'running',
        ];
        return $this->entityManager->getRepository(Task::class)->findBy($params);
        $currentTasks = [];
        foreach ($tasks as $task)
        {
            if ($this->isInWorkingTime($task) && $this->isInPause($task)) {
                $currentTasks [] = $task;
            }
        }
    }

}