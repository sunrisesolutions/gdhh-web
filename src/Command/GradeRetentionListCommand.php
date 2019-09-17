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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GradeRetentionListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:grade-retention')
            // the short description shown while running "php bin/console list"
            ->setDescription('Danh sach o lai lop')
            ->setDefinition(array(
                new InputArgument('chiDoan', InputArgument::REQUIRED, 'Enter Chi Doan'),
                new InputArgument('schoolYear', InputArgument::REQUIRED, 'Enter School Year')
            ))

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to migrate cnames of all Members...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cdNumber = $input->getArgument('chiDoan');
        $schoolYear = $input->getArgument('schoolYear');

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Start verifying data',
            '============',
            '',
        ]);
        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $cNameRepo = $this->getContainer()->get('doctrine')->getRepository(ChristianName::class);
        // $cacThanhVien = $this->getContainer()->get('doctrine')->getRepository(ThanhVien::class)->findBy([ 'tenThanh' => null ]);
        $cacThanhVien = $this->getContainer()->get('doctrine')->getRepository(ThanhVien::class)->findAll();
        $pbRepo = $this->getContainer()->get('doctrine')->getRepository(PhanBo::class);

        $output->writeln('FLAG 001');


        $cacPhanBo2019 = $pbRepo->findBy(['namHoc' => (int) $schoolYear]);
        $output->writeln('FLAG 002');
        $cdRepo = $this->getContainer()->get('doctrine')->getRepository(ChiDoan::class);


//		/** @var PhanBo180825 $pb */
//		foreach($cacPhanBo180825 as $pb) {
//			$cdId                                 = 'CD-ID: NULL';
//			$cacPhanBo180825Array[ $pb->getId() ] = null;
//			if( ! empty($pb->getChiDoan())) {
//				$cdId                                 = 'CD-ID: ' . $pb->getChiDoan()->getId();
//				$cacPhanBo180825Array[ $pb->getId() ] = $pb->getChiDoan();
//			}
//			$output->writeln([ 'phanbo180525', $pb->getId() . ' ' . $pb->getThanhVien()->getName() . ' ' . $cdId ]);
//		}
        if (count($cacPhanBo2019) === 0) {
            $output->writeln('empty pb2018 array');
        } else {
            $output->writeln('Found '.count($cacPhanBo2019));
        }

        $tatCaDcc = $this->getContainer()->get('doctrine')->getRepository(DiemChuyenCan::class)->findAll();
        $cacDccTheoThang = [];
        /** @var DiemChuyenCan $dcc */
        foreach ($tatCaDcc as $dcc) {
            $date = $dcc->getTargetDate();
            $dateNumber = (int) $date->format('m');

            if (!isset($cacDccTheoThang[$dateNumber]) || !is_array($cacDccTheoThang[$dateNumber])) {
                $cacDccTheoThang[$dateNumber] = [];
            }
            $cacDccTheoThang[$dateNumber][] = $dcc;
        }

        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        /** @var PhanBo $pb */
        foreach ($cacPhanBo2019 as $pb) {
            $cd = $pb->getChiDoan();
            $tv = $pb->getThanhVien();
            if (empty($pb->getPhanBoTruoc())) {
                continue;
            }
            if ($pb->getPhanBoTruoc()->getBangDiem()->isGradeRetention() || $pb->getPhanBoTruoc()->getBangDiem()->isGradeRetentionForced()) {
                if (empty($cdCu = $pb->getPhanBoTruoc()->getChiDoan())) {
                    $output->writeln('No CD cu '.$tv->getId().' '.$tv->getName());
                }
                if ($tv->isThieuNhi() && !empty($cd) && $cdCu->getNumber() == $cdNumber && $cdCu
                        ->getNumber() < 19 && $cd->getNumber() === $cdCu->getNumber()) {
                    $output->writeln('Grade Retention: '.$tv->getId().' '.$pb->getThanhVien()->getName());
                }
            }
        }


        ///////////////
        $output->writeln("No Flushing");
        $output->writeln("Successfully migrated all members.");
    }
}
