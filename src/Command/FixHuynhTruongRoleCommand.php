<?php

namespace App\Command;

use App\Entity\Backup\PhanBo180825;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\ChristianName;
use App\Entity\HoSo\DiemChuyenCan;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixHuynhTruongRoleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:fix-huynh-truong-role')
            // the short description shown while running "php bin/console list"
            ->setDescription('Fix HTR')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Start fixing role',
            '============',
            '',
        ]);

        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $cacThanhVien = $this->getContainer()->get('doctrine')->getRepository(ThanhVien::class)->findAll();
        /** @var ThanhVien $tv */
        foreach ($cacThanhVien as $tv) {
            $pb = $tv->getPhanBoNamNay();
            if ($pb) {
                $pb->setVaiTro();
            }
        }

        $output->writeln("Flushing");
        $manager->flush();
        $output->writeln("Successfully migrated all members.");
    }
}
