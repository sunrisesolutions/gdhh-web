<?php

namespace App\Command;

use App\Entity\HuynhTruong;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CountVotesCommand extends Command
{
    protected static $defaultName = 'app:count-votes';

    private $em;

    public function __construct(string $name = null, EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

            $cacTruong = $this->em->getRepository(HuynhTruong::class)->findAll();
            $io->writeln('Updating Info '.rand(0, 100));
            /** @var HuynhTruong $truong */
            foreach ($cacTruong as $truong) {
                $truong->updateVoteCount();
                $this->em->persist($truong);
            }
            $this->em->flush();

        return 0;
    }
}
