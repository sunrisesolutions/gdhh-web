<?php

namespace App\Command;

use App\Entity\HocBa\BangDiem;
use App\Entity\HoSo\ChristianName;
use App\Entity\HoSo\DiemChuyenCan;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitiateDiligencePointCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			// the name of the command (the part after "bin/console")
			->setName('app:initiate-diligence-point')
			// the short description shown while running "php bin/console list"
			->setDescription('Recalculating Grades')
			->setDefinition(array(
				new InputArgument('schoolYear', InputArgument::REQUIRED, 'Enter School Year')
			))
			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to initiate-diligence-point...');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$schoolYear = $input->getArgument('schoolYear');
		// outputs multiple lines to the console (adding "\n" at the end of each line)
		$output->writeln([
			'Start initiating diligence point',
			'============',
			'',
		]);
		$manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
		$namHoc  = $this->getContainer()->get('doctrine')->getRepository(NamHoc::class)->find($schoolYear);

//		$repo = $this->getContainer()->get('doctrine')->getRepository(DiemChuyenCan::class);
		
		$schoolYear     = (int) $schoolYear;
		$schoolYearDate = new \DateTime();
		$schoolYearDate->setDate($schoolYear, 9, 15);
		$nextYearDate = new \DateTime();
		$nextYearDate->setDate($schoolYear + 1, 6, 1);
		
		$qb = $this->getContainer()->get('doctrine.orm.default_entity_manager')->createQueryBuilder();
		$qb->select('dcc')->from(DiemChuyenCan::class, 'dcc');
		$qb->where('dcc.targetDate BETWEEN :schoolYear AND :nextYear')
		   ->setParameter('schoolYear', $schoolYearDate->format('Y-m-d'))
		   ->setParameter('nextYear', $nextYearDate->format('Y-m-d'));

//$qb->where('e.fecha > :monday')
//   ->andWhere('e.fecha < :sunday')
//   ->setParameter('monday', $monday->format('Y-m-d'))
//   ->setParameter('sunday', $sunday->format('Y-m-d'));
		
		$result = $qb->getQuery()->getResult();
		$output->writeln('schoolYear is ' . $schoolYearDate->format('Y-m-d'));
		/** @var DiemChuyenCan $dcc */
//		foreach($result as $dcc) {
//			$x = $dcc;
//			$output->writeln([ 'dcc', $dcc->getTargetDate()->format('Y-m-d') ]);
//			if($dow = strtoupper($dcc->getTargetDate()->format('l')) == 'THURSDAY') {
//				$output->writeln('this is a thursday');
//			};
//		}
		
		while($schoolYearDate <= $nextYearDate) {
			$dowT = clone $schoolYearDate;
			$qb   = $this->getContainer()->get('doctrine.orm.default_entity_manager')->createQueryBuilder();
			$qb->select('dcc')->from(DiemChuyenCan::class, 'dcc');
			$expr = $qb->expr();
			$qb->where($expr->eq('dcc.targetDate', ':date'))
			   ->setParameter('date', $dowT->format('Y-m-d'));
			
			/** @var DiemChuyenCan $dowDiemCC */
			$dowDiemCC = $qb->getQuery()->getOneOrNullResult();
			$dowDiemCC = $this->initiateDCC($dowT, $dowDiemCC);
			$manager->persist($dowDiemCC);
			
			$dowS = clone $schoolYearDate;
			$dowS->modify('+4 days');
			if($dowS > $nextYearDate) {
				break;
			}
			$qb = $this->getContainer()->get('doctrine.orm.default_entity_manager')->createQueryBuilder();
			$qb->select('dcc')->from(DiemChuyenCan::class, 'dcc');
			$expr = $qb->expr();
			$qb->where($expr->eq('dcc.targetDate', ':date'))
			   ->setParameter('date', $dowS->format('Y-m-d'));
			
			/** @var DiemChuyenCan $dowDiemCC */
			$dowDiemCC = $qb->getQuery()->getOneOrNullResult();
			$dowDiemCC = $this->initiateDCC($dowS, $dowDiemCC);
			$manager->persist($dowDiemCC);
			
			$schoolYearDate->modify('+7 days');
		}
		
		$diemCC = new DiemChuyenCan();
		
		$output->writeln("Flushing");
		$manager->flush();
		$output->writeln("Successfully recalculated all student grades for " . $schoolYear);
	}
	
	private function initiateDCC(\DateTime $dow, DiemChuyenCan $dowDiemCC = null) {
		if(empty($dowDiemCC)) {
			$dowDiemCC = new DiemChuyenCan();
			$dowDiemCC->setTargetDate($dow);
		}
		$point = null;
		if(strtoupper($dowDiemCC->getTargetDate()->format('l')) == 'THURSDAY') {
			$point = 1;
		} elseif(strtoupper($dowDiemCC->getTargetDate()->format('l')) == 'SUNDAY') {
			$point = 2;
		}
		$dowDiemCC->setPointValue($point);
		
		return $dowDiemCC;
	}
}
