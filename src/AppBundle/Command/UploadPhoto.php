<?php


namespace AppBundle\Command;


use AppBundle\Service\PhotoService;
use getjump\Vk\Core;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadPhoto extends Command
{
    private $photoService;
    private $vk;
    public function __construct(PhotoService $photoService, Core $vk, $name = null)
    {
        parent::__construct($name);

        $this->photoService = $photoService;
        $this->vk = $vk;
        $this->vk->setToken('fbb3115711ee27ad33a15adec5a4184ee3a1adf0704a1032fd52d1d93b89a61014fcd3706378fefa02916');
    }

    protected function configure()
    {
        $this
            ->setName('app:upload-photo')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->photoService->getAlbums(955435, $this->vk);
    }
}