<?php
namespace AppBundle\Command;

use AppBundle\Entity\Attachment;
use AppBundle\Entity\WallPost;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use getjump\Vk\Core;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrimaryWallParse extends Command
{
    /**
     * Entity Manager
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Core
     */
    private $vk;
    public function __construct(Core $vk, EntityManagerInterface $em, $name = null)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->vk = $vk;
    }

    protected function configure()
    {
        $this
            ->setName('app:primary-wall-copy')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->vk->request('wall.get', ['owner_id' => 955435, 'count' => 100, 'offset' => 0])->each(function($i, $v) {
            if ($v->post_type == 'post') {
                print_r($v);
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
}