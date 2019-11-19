<?php

namespace App\Command;

use League\Flysystem\FilesystemInterface;
use Nbt\Node;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BlockSetCommand extends Command
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

    protected static $defaultName = 'block:set';

    protected function configure()
    {
        $this
            ->setDescription('Set the block in the world')
            ->addArgument('block', InputArgument::REQUIRED, 'Block name, like minecraft:grass_block')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $structure = "CgAACQAEc2l6ZQMAAAADAAAAAQAAAAEAAAABCQAIZW50aXRpZXMAAAAAAAkABmJsb2NrcwoAAAABCQADcG9zAwAAAAMAAAAAAAAAAAAAAAADAAVzdGF0ZQAAAAAACQAHcGFsZXR0ZQoAAAABCAAETmFtZQANbWluZWNyYWZ0OmFpcgADAAtEYXRhVmVyc2lvbgAAB7gA";

        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('block');

        $nbtService = new \Nbt\Service(new \Nbt\DataHandler());
        $nbt = gzdecode($this->defaultStorage->read('set.nbt'));
        $structure = $nbtService->readString(base64_decode($structure));

        $palette = $structure->findChildByName('palette');
        $paletteChildren = $palette->getChildren();
        $paletteChildren[0]->getChildren()[0]->setValue($arg1);

        $this->defaultStorage->update('set.nbt', gzencode($nbtService->writeString($structure)));


        //$this->defaultStorage->update('set.nbt', gzencode($newNbt));

        $io->success('You placed a block in Minecraft!');

        return 0;
    }
}
