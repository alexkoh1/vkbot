<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Task;
use Doctrine\ORM\EntityManager;

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
    public function getVkIdOfLastPostedRecord(Task $task): int
    {
        $fromId = $task->getFromId();
        $entity = $this->entityManager
            ->getRepository('AppBundle:TaskLog')
            ->createQueryBuilder('e')
            ->join('e.recordId', 'r')
            ->where('r.fromId = '.$fromId)
            ->select('r.vkId')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($entity) {
            return $entity->vkId;
        } else {
            return 0;
        }
    }
}