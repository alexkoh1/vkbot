<?php


namespace AppBundle\Command;


use AppBundle\Service\PhotoService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadPhoto extends Command
{
    private $photoService;
    public function __construct(PhotoService $photoService, $name = null)
    {
        parent::__construct($name);
        $this->photoService = $photoService;
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
        $this->photoService->uploadPhoto();
    }
}