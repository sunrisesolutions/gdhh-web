<?php

namespace App\Command;

use App\Entity\Backup\PhanBo180825;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\ChristianName;
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
            'Start migrating',
            '============',
            '',
        ]);
        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $cNameRepo = $this->getContainer()->get('doctrine')->getRepository(ChristianName::class);
        // $cacThanhVien = $this->getContainer()->get('doctrine')->getRepository(ThanhVien::class)->findBy([ 'tenThanh' => null ]);
        $cacThanhVien = $this->getContainer()->get('doctrine')->getRepository(ThanhVien::class)->findAll();
        $pbRepo = $this->getContainer()->get('doctrine')->getRepository(PhanBo::class);
        $cacPhanBo2018 = $pbRepo->findBy(['namHoc' => 2018]);
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

        /** @var PhanBo $pb */
        foreach ($cacPhanBo2018 as $pb) {
            $cd = $pb->getChiDoan();
            if(!empty($cd) && $cd->getNumber() === 12){
                $bd = $pb->getBangDiem();
                $st1 = $bd->getSundayTicketTerm1();
                $bd->tinhDiemChuyenCan(1);
                $st1b = $bd->getSundayTicketTerm1();
                if($st1 !== $st1b){
                    $output->writeln('Wrong ticket numbers for the First Semester: ' . $st1 . ' ' . $st1b);
                }else{
//                    $output->writeln('Correct ticket numbers: ' . $st1 . ' ' . $st1b);
                }
    
                $st2 = $bd->getSundayTicketTerm1();
                $bd->tinhDiemChuyenCan(2);
                $st2b = $bd->getSundayTicketTerm1();
                if($st2 !== $st2b){
                    $output->writeln('Wrong ticket numbers for the Second Semester: ' . $st2 . ' ' . $st2b);
                }
            }
        }
        
        $namHoc2016 = $this->getContainer()->get('doctrine')->getRepository(NamHoc::class)->find(2016);


        ///////////////
        $output->writeln("Flushing");
        $manager->flush();
        $output->writeln("Successfully migrated all members.");
    }
}
