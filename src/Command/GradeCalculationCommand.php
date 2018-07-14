<?php

namespace App\Command;

use App\Entity\HocBa\BangDiem;
use App\Entity\HoSo\ChristianName;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GradeCalculationCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			// the name of the command (the part after "bin/console")
			->setName('app:calculate-grade')
			// the short description shown while running "php bin/console list"
			->setDescription('Recalculating Grades')
			->setDefinition(array(
				new InputArgument('schoolYear', InputArgument::REQUIRED, 'Enter School Year')
			))
			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to Recalculating Grades...');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$schoolYear = $input->getArgument('schoolYear');
		// outputs multiple lines to the console (adding "\n" at the end of each line)
		$output->writeln([
			'Start Recalculating',
			'============',
			'',
		]);
		$manager   = $this->getContainer()->get('doctrine.orm.default_entity_manager');
		$namHoc    = $this->getContainer()->get('doctrine')->getRepository(NamHoc::class)->find($schoolYear);
		$cacPhanBo = $namHoc->getPhanBoHangNam();
		/** @var PhanBo $pb */
		foreach($cacPhanBo as $pb) {
			if( ! empty($pb->getChiDoan())) {
				$bd = $pb->getBangDiem();
				if( ! empty($bd)) {
					$truoc = [
						$bd->getTbCCTerm1(),
						$bd->getTbCCTerm2(),
						$bd->getTbGLTerm1(),
						$bd->getTbGLTerm2(),
						$bd->getTbGLYear(),
						$bd->getTbYear(),
						$bd->getCategoryTrans(),
						$bd->getRemarks()
					];
					
					$bd->tinhDiemHocKy(1);
					$bd->tinhDiemHocKy(2);
					
					$sau      = [
						$bd->getTbCCTerm1(),
						$bd->getTbCCTerm2(),
						$bd->getTbGLTerm1(),
						$bd->getTbGLTerm2(),
						$bd->getTbGLYear(),
						$bd->getTbYear(),
						$bd->getCategoryTrans(),
						$bd->getRemarks()
					];
					$truocStr = implode(' - ', $truoc);
					$sauStr   = implode(' - ', $sau);
					if($truocStr !== $sauStr) {
						$output->writeln([ 'Truoc ', $truocStr, 'Sau ', $sauStr ]);
					}
					$manager->persist($bd);
				}
			}
		}
		
		$output->writeln("Flushing");
		$manager->flush();
		$output->writeln("Successfully recalculated all student grades for " . $schoolYear);
	}
}