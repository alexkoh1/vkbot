<?php


namespace AppBundle\Service;


use AppBundle\Entity\Attachment;
use AppBundle\Entity\WallPost;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use getjump\Vk\Core;

class WallService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PhotoService
     */
    private $photoService;

    /**
     * WallService constructor.
     *
     * @param PhotoService           $photoService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(PhotoService $photoService, EntityManagerInterface $entityManager)
    {
        $this->photoService = $photoService;
        $this->entityManager = $entityManager;
    }

    /**
     * Получает запись со стены пользователя
     *
     * @param $count
     * @param $offSet
     *
     * @return mixed
     */
    public function getPostsFromWall($count, $offSet, $ownerId, Core $vk) {
        $params = [
            'owner_id' => $ownerId,
            'count'    => $count,
            'offset'   => $offSet,
            'filter'  => 'owner',
        ];
        $result = $vk->request('wall.get', $params)->getResponse();
        rsort($result);
        return $result;
    }

    /**
     * Создаёт строку для загрузки файлов через API
     *
     * @param WallPost $wallPost
     * @param Core     $vk
     *
     * @return string
     */
    public function createAttachmentToPost(WallPost $wallPost, Core $vk, string $ownerId){
        $attachments   = $wallPost->getAttachments();
        $newAttachments = '';
        foreach ($attachments as $attachment) {
            $newAttachments = $newAttachments.$this->getNewMediaId($attachment, $vk, $ownerId).',';
        }

        return rtrim($newAttachments, ',');
    }

    /**
     * @param Attachment $attachment
     * @param Core       $vk
     *
     * @return string
     */
    private function getNewMediaId(Attachment $attachment, Core $vk, $ownerId) {
        if ($attachment->getType() == "photo") {
            $photoId = $attachment->getPost()->getFromId() . '_' . $attachment->getUrl();
            $tempPhotoPath = $this->photoService->downloadPhoto($photoId, $vk);
            $newPhotoId    = $this->photoService->uploadPhoto($tempPhotoPath, $vk, $ownerId);
            return $newPhotoId;
        } else {
            $newAttachments = $attachment->getPost()->getFromId() . '_' . $attachment->getUrl();
            return $attachment->getType().$newAttachments;
        }
    }

    /**
     * Добавляет запись из вк в базу данных
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