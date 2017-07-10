<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Attachment;
use AppBundle\Entity\WallPost;
use Doctrine\Common\Collections\ArrayCollection;

class PostRepository
{
    public function __construct(

    )
    {

    }

    /**
     * Добавляет в базу данных
     *
     * @param $record
     */
    public function addRecordToDb($record)
    {
        $wallPost = new WallPost();

        $wallPost->setVkId($record->id);
        $wallPost->setFromId($record->from_id);
        $wallPost->setToId($record->to_id);
        $wallPost->setPostType($record->post_type);
        $wallPost->setText($record->text);
        $wallPost->setDate($record->date);

        $postAttachment = new ArrayCollection();

        $attachments = $record->attachments ?? null;

        foreach ((array)$attachments as $key => $attachment) {
            $allowAttachments = ['photo', 'posted_photo', 'audio', 'video'];
            $postType = $attachment->type;
            if (!in_array($postType, $allowAttachments)) {
                continue;
            }
            $atomAttachment = new Attachment();
            $atomAttachment->setType($postType);
            $atomAttachment->setUrl($attachment->$postType->id);
            $atomAttachment->setPost($wallPost);
            $postAttachment->add($atomAttachment);
        }

        $wallPost->setAttachments($postAttachment);
        $this->entityManager->persist($wallPost);
        $this->entityManager->flush();
    }
}