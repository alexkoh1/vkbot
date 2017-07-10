<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Task;
use AppBundle\Entity\WallPost;
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
     * Получает из базы пост для постинга
     */
    public function getPostToCreate($sourceVkId, $lastPostVkId) {
        $post = $this->entityManager
            ->getRepository('AppBundle:WallPost')
            ->createQueryBuilder('e')
            ->where('e.fromId = '.$sourceVkId)
            ->andWhere('e.vkId > '.$lastPostVkId)
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }


    /**
     * Получает vkId последней опубликованной от имени определенного пользователя записи
     *
     * @param Task $task
     *
     * @return WallPost
     */
    public function getNextPost(Task $task): WallPost
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
            $lastPostVkId = $entity['vkId'];
        } else {
            $lastPostVkId = 0;
        }

        $post = $this->entityManager
            ->getRepository('AppBundle:WallPost')
            ->createQueryBuilder('e')
            ->where('e.fromId = '.$fromId)
            ->andWhere('e.vkId > '.$lastPostVkId)
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        return $post;
    }
}