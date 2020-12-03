<?php

namespace App\Command;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TimeDayCommand extends Command
{
    /**
     * @var FilesystemInterface
     */
    private $defaultStorage;

    public function __construct(FilesystemInterface $defaultStorage)
    {
        parent::__construct();
        $this->defaultStorage = $defaultStorage;
    }

    protected static $defaultName = 'time:day';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $structure = "CgAACQAEc2l6ZQMAAAADAAAAAQAAAAEAAAABCQAIZW50aXRpZXMAAAAAAAkABmJsb2NrcwoAAAABCQADcG9zAwAAAAMAAAAAAAAAAAAAAAADAAVzdGF0ZQAAAAAACQAHcGFsZXR0ZQoAAAABCAAETmFtZQANbWluZWNyYWZ0OmFpcgADAAtEYXRhVmVyc2lvbgAAB7gA";

        $io = new SymfonyStyle($input, $output);

        $nbtService = new \Nbt\Service(new \Nbt\DataHandler());
        $structure = $nbtService->readString(base64_decode($structure));

        $palette = $structure->findChildByName('palette');
        $paletteChildren = $palette->getChildren();
        $paletteChildren[0]->getChildren()[0]->setValue('minecraft:redstone_block');

        $this->defaultStorage->update('time_day.nbt', gzencode($nbtService->writeString($structure)));

        $paletteChildren[0]->getChildren()[0]->setValue('minecraft:black_concrete');

        $this->defaultStorage->update('time_night.nbt', gzencode($nbtService->writeString($structure)));

        $io->success('Set time to day');

        return 0;
    }
}
