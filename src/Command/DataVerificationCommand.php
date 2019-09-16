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

class DataVerificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:verify-data')
            // the short description shown while running "php bin/console list"
            ->setDescription('Verify Data for Chi Doan 12 - Bang Diem')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to migrate cnames of all Members...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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


        $cacPhanBo2019 = $pbRepo->findBy(['namHoc' => 2019]);
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
            $tv= $pb->getThanhVien();
            if ($pb->getPhanBoTruoc()->getBangDiem()->isGradeRetention()) {
                if(empty($pb->getPhanBoTruoc()->getChiDoan())){
                    $output->writeln('No CD cu '.$tv->getId().' '.$tv->getName());
                }
                if (!empty($cd) && $cd->getNumber() !== $pb->getPhanBoTruoc()->getChiDoan()->getNumber()) {
                    $output->writeln('WRONG Grade Retention for '.$tv->getId(). ' '.$pb->getThanhVien()->getName());
                }
            }
            if (!empty($cd)) {
                if ($cd->getNumber() === 12) {
                    $bd = $pb->getBangDiem();
                    $st1 = $bd->getSundayTicketTerm1();
                    $st2 = $bd->getSundayTicketTerm2();

                    $bangDiem = $bd;
                    $bangDiem->setSundayTicketTerm1(0);
                    $bangDiem->setSundayTicketTerm2(0);
                    $bangDiem->setSundayTickets(0);
                    foreach ($cacDccTheoThang as $cacDcc) {
                        $bangDiem->tinhDiemChuyenCanThang($cacDcc);
                        $bangDiem->tinhPhieuLeCNThang($cacDcc);
                    }
                    $bangDiem->tinhDiemChuyenCan(1);
                    $bangDiem->tinhDiemHocKy(1);
                    $bangDiem->tinhDiemChuyenCan(2);
                    $bangDiem->tinhDiemHocKy(2);

//                    $manager->persist($bangDiem);


                    $st1b = $bd->getSundayTicketTerm1();
                    if ($st1 !== $st1b) {
//                        $output->writeln('Wrong ticket numbers for the First Semester: '.$st1.' '.$st1b.' - '.$pb->getThanhVien()->getName());
                    } else {
//                        $output->writeln('Correct ticket numbers 1: ' . $st1 . ' ' . $st1b);
                    }

                    $bd->tinhDiemChuyenCan(2);
                    $st2b = $bd->getSundayTicketTerm2();
                    if ($st2 !== $st2b) {
//                        $output->writeln('Wrong ticket numbers for the Second Semester: '.$st2.' '.$st2b.' - '.$pb->getThanhVien()->getName());
                    } else {
//                        $output->writeln('Correct ticket numbers 2: ' . $st2 . ' ' . $st2b);
                    }
                } else {
//                    $output->writeln('cdNumber '.$cd->getNumber());
                }
            } else {
                $output->writeln('hello empty cd '.$pb->getThanhVien()->getName());
            }
        }


        ///////////////
        $output->writeln("No Flushing");
        $output->writeln("Successfully migrated all members.");
    }
}
