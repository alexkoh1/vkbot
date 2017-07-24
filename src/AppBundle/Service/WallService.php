<?php


namespace AppBundle\Service;


use AppBundle\Entity\Attachment;
use AppBundle\Entity\WallPost;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use getjump\Vk\Core;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;

class WallService
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var PhotoService
     */
    private $photoService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * WallService constructor.
     *
     * @param PhotoService  $photoService
     * @param EntityManager $entityManager
     * @param Core          $vk
     * @param Logger        $logger
     */
    public function __construct(
        PhotoService $photoService,
        EntityManager $entityManager,
        Core $vk,
        Logger $logger
    ) {
        $this->photoService  = $photoService;
        $this->entityManager = $entityManager;
        $this->vk            = $vk;
        $this->logger        = $logger;
    }

    /**
     * Получает запись со стены пользователя
     *
     * @param $count
     * @param $offSet
     *
     * @return mixed
     */
    public function getPostsFromWall($count, $offSet, $ownerId) {
        $this->vk->setToken('fbb3115711ee27ad33a15adec5a4184ee3a1adf0704a1032fd52d1d93b89a61014fcd3706378fefa02916');
        $params = [
            'owner_id' => $ownerId,
            'count'    => $count,
            'offset'   => $offSet,
            'filter'  => 'owner',
        ];
        $result = $this->vk->request('wall.get', $params)->getResponse();
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

        if ($attachments->isEmpty()) {
            return null;
        }
        $newAttachments = '';
        foreach ($attachments as $attachment) {
            $newAttachments = $newAttachments.$this->getNewMediaId($attachment, $vk, $ownerId).',';
            sleep(1);
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

    /**
     * Создаёт пост на стене ВК
     *
     * @param int      $destinationVkId
     * @param WallPost $post
     *
     * @return mixed
     */
    public function createPost(int $destinationVkId, WallPost $post)
    {
        $ownerId = $destinationVkId;
        $vkToken = $this->entityManager->getRepository('AppBundle\Entity\Bot')->findOneByVkId($destinationVkId);
        $this->vk->setToken($vkToken->getAccessToken());

        if ($attachment = $this->createAttachmentToPost($post, $this->vk, $ownerId)) {
            $params = [
                'owner_id' => $ownerId,
                'message' => $post->getText(),
                'attachments' => $attachment
            ];
        } else {
            $params = [
                'owner_id' => $ownerId,
                'message' => $post->getText(),
            ];
        }

        $res = $this->vk->request('wall.post', $params)->getResponse();

        return $res->post_id;
    }
}