<?php

namespace App\Command;

use App\Entity\CuTri;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GeneratePinCommand extends Command
{
    protected static $defaultName = 'app:generate:pin';

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

        $io->note('Generating 200 CuTri ');
        for ($i = 0; $i < 200; $i++) {
            $pins = $this->generatePin();
            $voter = new CuTri();
            $voter->setPin($pins[0]);
            $voter->setPinFormatted($pins[1]);
            $this->em->persist($voter);
        }
        $this->em->flush();

        return 0;
    }

    public function generatePin()
    {
        $pin = ''.rand(0, 999999999);
//        $io->note($pin.' '.strlen($pin));
        for ($i = 0; $i < 9 - strlen($pin); $i++) {
            $pin = '0'.$pin;
//            $io->note('hello '.$pin);
        }

        ($pinArray = str_split($pin));
        $formattedPin = '';
        foreach ($pinArray as $i => $digit) {
            $pos = $i + 1;
//            $io->note($digit.' '.$i.'  '.($pos % 3));
            $formattedPin .= $digit;
            if ($pos % 3 === 0 && $pos < strlen($pin)) {
                $formattedPin .= '-';
            }
        }

//        $io->success($formattedPin);
        return [$pin, $formattedPin];
    }
}
