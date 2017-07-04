<?php
namespace AppBundle\Command;

use AppBundle\AppBundle;
use AppBundle\Entity\Attachment;
use AppBundle\Entity\WallPost;
use AppBundle\Service\WallService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use getjump\Vk\Core;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WallParse extends Command
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
     * @var WallService
     */
    private $wallService;

    /**
     * @var string
     */
    private $ownerId;

    private $logger;

    public function __construct(WallService $wallService, Core $vk, EntityManagerInterface $entityManager, $name = null)
    {
        parent::__construct($name);
        $this->wallService   = $wallService;
        $this->entityManager = $entityManager;
        $this->vk            = $vk;
        $this->vk->setToken('fbb3115711ee27ad33a15adec5a4184ee3a1adf0704a1032fd52d1d93b89a61014fcd3706378fefa02916');
    }

    protected function configure()
    {
        $this
            ->setName('app:wall-parse')
            ->setDescription('Copy records from wall to database')
            ->setHelp('This command copy records from user`s wall to local database.')
            ->addArgument('source_vk_id', InputArgument::REQUIRED, 'Please, enter source vk id.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ownerId = $input->getArgument('source_vk_id');
        $recordsFromVk = $this->wallService->getPostsFromWall(20, 0, $this->ownerId, $this->vk);

        foreach ($recordsFromVk as $record) {
            $params = [
                'vkId'    => $record->id,
                'fromId' => $this->ownerId,
            ];
            $post = $this->entityManager->getRepository('AppBundle\Entity\WallPost')->findBy($params);
            if ($record->post_type == 'post' && !$post) {
                $this->wallService->addRecordToDb($record);
                //$this->logger->addInfo('Vk post');
            }
        }
    }
}
