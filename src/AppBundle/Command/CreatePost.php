<?php
namespace AppBundle\Command;

use AppBundle\AppBundle;
use AppBundle\Entity\Attachment;
use AppBundle\Entity\WallPost;
use AppBundle\Service\PhotoService;
use AppBundle\Service\WallService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use getjump\Vk\Core;
use getjump\Vk\Model\Wall;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePost extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Core
     */
    private $vk;

    /**
     * @var PhotoService
     */
    private $photoService;

    /**
     * @var WallService
     */
    private $wallService;

    /**
     * @var string
     */
    private $ownerId;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * CreatePost constructor.
     *
     * @param Core                   $vk
     * @param EntityManagerInterface $entityManager
     * @param PhotoService           $photoService
     * @param WallService            $wallService
     * @param null                   $name
     * @param Logger                 $logger
     */
    public function __construct(
            Core $vk,
            EntityManagerInterface $entityManager,
            PhotoService $photoService,
            WallService $wallService,
            Logger $logger,
            $name = null
    ) {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->photoService  = $photoService;
        $this->wallService   = $wallService;
        $this->logger        = $logger;
        $this->vk = $vk;
    }


    protected function configure()
    {
        $this
            ->setName('app:create-post')
            ->setDescription('Creates a new post to wall.')
            ->setHelp('This command create a new post to wall.')
            ->addArgument('destination_vk_id', InputArgument::REQUIRED, 'Please, enter destination vk id.')
            ->addArgument('source_vk_id', InputArgument::REQUIRED, 'Please, enter source vk id. I should present in the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destinationVkId = $input->getArgument('destination_vk_id');
        $sourceVkId      = $input->getArgument('source_vk_id');
        $this->ownerId   = $destinationVkId;

        $vkToken = $this->entityManager->getRepository('AppBundle\Entity\Bot')->findOneByVkId($destinationVkId);
        $this->vk->setToken($vkToken->getAccessToken());

        $params = [
           'isPosted' => 0,
           'fromId'  => $input->getArgument('source_vk_id'),
        ];
        $posts = $this->entityManager->getRepository('AppBundle\Entity\WallPost')->findBy($params);

        foreach ($posts as $post) {
            //$attachments = $post->getAttachments();
            $attachment = $this->wallService->createAttachmentToPost($post, $this->vk, $this->ownerId);
            $params = [
                'owner_id' => $this->ownerId.'1',
                'message' => $post->getText(),
                'attachments' => $attachment,
            ];
            $res = $this->vk->request('wall.post', $params)->getResponse();
            $post->setIsPosted(1);
            $this->entityManager->flush();
            $this->logger->addInfo('Wall post https://vk.com/wall'.$post->getFromId().'_'.$post->getVkId(). ' was copied to https://vk.com/wall'.$this->ownerId.'_'.$res->post_id);
        }
    }
}