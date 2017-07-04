<?php
namespace AppBundle\Command;

use AppBundle\AppBundle;
use AppBundle\Entity\Attachment;
use AppBundle\Entity\WallPost;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use getjump\Vk\Core;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WallParse extends Command
{
    private $em;
    private $vk;
    public function __construct(EntityManagerInterface $em, $name = null)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->vk = Core::getInstance()->apiVersion('5.5')->setToken('4c3bb1a99107b7b57fccb8da53f37eae1eb8dc61ae15099c7940524b7ddcc2345e008eb46c74ac0cbf59a');

    }

    protected function configure()
    {
        $this
            ->setName('app:create-user')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new user.')

            ->setHelp('This command allows you to create a user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lastRecordFromVk    = $this->getLastRecordFromVk(0);
        $lastRecordFromDb = $this->getLastRecordFromDb();

        $count = $lastRecordFromVk->id - $lastRecordFromDb->getVkId();

        $this->vk->request('wall.get', ['owner_id' => 955435, 'count' => $count-1])->each(function($i, $v) {
            $post = $this->em->getRepository('AppBundle\Entity\WallPost')->findByVkId($v->id);
            if ($v->post_type == 'post' and !$post) {
                $this->addRecordToDb($v);
            }
        });
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