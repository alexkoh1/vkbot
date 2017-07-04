<?php
namespace AppBundle\Command;

use AppBundle\AppBundle;
use AppBundle\Entity\Attachment;
use AppBundle\Entity\WallPost;
use AppBundle\Service\PhotoService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use getjump\Vk\Core;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePost extends Command
{
    private $em;
    private $vk;
    private $photoService;
    public function __construct(Core $vk, EntityManagerInterface $em, PhotoService $photoService, $name = null)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->vk = $vk;
        $this->photoService = $photoService;

    }

    protected function configure()
    {
        $this
            ->setName('app:create-post')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new user.')

            ->setHelp('This command allows you to create a user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $posts = $this->em->getRepository('AppBundle\Entity\WallPost')->findBy(['isPosted' => 0]);
        foreach ($posts as $post) {
            //$attachments = $post->getAttachments();
            $attachment = $this->createAttachmentToPost($post);
            print_r($attachment);
            $params = [
                'owner_id' => '413686536',
                'message' => $post->getText(),
                'attachments' => $attachment,
            ];
            $es = $this->vk->request('wall.post', $params)->getResponse();
            $post->setIsPosted(1);
            $this->em->flush();
            print_r($es);
        }
    }

    private function createAttachmentToPost(WallPost $wallPost){
        $attachments = $wallPost->getAttachments();
        $newAttachments = '';
        foreach ($attachments as $attachment) {
                $newAttachments = $newAttachments.$this->getNewMediaId($attachment).',';
        }

        return rtrim($newAttachments, ',');
    }
    private function getNewMediaId($attachment) {
        if ($attachment->getType() == "photo") {
            $newAttachments = $attachment->getPost()->getFromId() . '_' . $attachment->getUrl();
            $params         = [
                               'photos' => $newAttachments,
                               'photo_sizes' => 1,
                              ];
            $res = $this->vk->request('photos.getById', $params)->getResponse();
            $photoUrl = end($res[0]->sizes)->src;
            $tempFilePath = '/tmp/tempfile.jpg';
            file_put_contents($tempFilePath, fopen("$photoUrl", 'r'));
            $newPhotoId = $this->photoService->uploadPhoto($tempFilePath);
            return $newPhotoId;
        } else {
            $newAttachments = $attachment->getPost()->getFromId() . '_' . $attachment->getUrl();
            return $attachment->getType().$newAttachments;
        }
    }

    private function createPostAttachments(WallPost $post) {
        $attachments = $post->getAttachments();
        /*foreach ($attachments as $attachment) {
            $attachment->
        }*/
    }
    private function addRecordToDb($record)
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
        $this->em->persist($wallPost);
        $this->em->flush();
    }

    private function getLastRecordFromVk($offset) {
        $requestParam = [
            'owner_id' => 955435,
            'count' => 1,
            'offset' => $offset,
            'filter' => 'owner',
        ];
        return $this->vk->request('wall.get', $requestParam)
            ->fetchData()
            ->one();

    }
    private function getLastRecordFromDb() {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from('AppBundle:WallPost', 'e')
            ->orderBy('e.vkId', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}