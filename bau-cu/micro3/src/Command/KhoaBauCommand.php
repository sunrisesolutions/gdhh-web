<?php

namespace App\Command;

use App\Entity\HuynhTruong;
use App\Entity\NhiemKy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class KhoaBauCommand extends Command
{
    protected static $defaultName = 'app:khoa-bau';

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

        /** @var NhiemKy $nhiemKy */
        $nhiemKy = $this->em->getRepository(NhiemKy::class)->findOneByEnabled(true);
        if (!empty($nhiemKy)) {
            $cacTruong = $this->em->getRepository(HuynhTruong::class)->findBy(['year' => $nhiemKy->getYear(), 'enabled' => true,
            ]);
            /** @var HuynhTruong $truong */
            foreach ($cacTruong as $truong) {
                $truong->updateVoteCount($nhiemKy);
                $truong->updateVotesHienTai($nhiemKy);
                $this->em->persist($truong);
            }
            $nhiemKy->{'setVong'.$nhiemKy->getVongHienTai()}(false);

            $this->em->persist($nhiemKy);
            $this->em->flush();
        }

        return 0;
    }
}
