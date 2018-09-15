<?php

namespace App\Command;

use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\ChristianName;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			// the name of the command (the part after "bin/console")
			->setName('app:migrate')
			// the short description shown while running "php bin/console list"
			->setDescription('Migrate CNames and Missing phanbo in ThanhVien')
			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to migrate cnames of all Members...');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		// outputs multiple lines to the console (adding "\n" at the end of each line)
		$output->writeln([
			'Start migrating',
			'============',
			'',
		]);
		$manager   = $this->getContainer()->get('doctrine.orm.default_entity_manager');
		$cNameRepo = $this->getContainer()->get('doctrine')->getRepository(ChristianName::class);
		// $cacThanhVien = $this->getContainer()->get('doctrine')->getRepository(ThanhVien::class)->findBy([ 'tenThanh' => null ]);
		$cacThanhVien = $this->getContainer()->get('doctrine')->getRepository(ThanhVien::class)->findAll();
		$cacPhanBo    = $this->getContainer()->get('doctrine')->getRepository(PhanBo::class)->findAll();
		$cdRepo       = $this->getContainer()->get('doctrine')->getRepository(ChiDoan::class);
		
		/** @var PhanBo $pb */
		foreach($cacPhanBo as $pb) {
			if( ! empty($dngl = $pb->getDoiNhomGiaoLy())) {
				if($dngl->getChiDoan() !== $pb->getChiDoan()) {
					$output->writeln("Fixing Chi doan data for " . $pb->getThanhVien()->getName());
					$pb->setChiDoan($dngl->getChiDoan());
					$manager->persist($pb);
				}
			} else {
				$incorrectChiDoanNumber = $pb->getChiDoan()->getNumber();
				$cdNumber               = $pb->getThanhVien()->getChiDoan();
				if($cdNumber !== $incorrectChiDoanNumber) {
					$cd = $cdRepo->findOneBy([ 'number' => $cdNumber, 'namHoc' => 2018 ]);
					if( ! empty($cd)) {
						$output->writeln('fixing chidoan for ' . $pb->getThanhVien()->getName() . ' set CD to ' . $cdNumber . '-2018');
						$pb->setChiDoan($cd);
						$manager->persist($pb);
					} else {
						$output->writeln('cannot find cd ' . $cdNumber . '-2018');
					}
				}
			}
		}
		
		///////////////
		
		/** @var ThanhVien $tv */
		foreach($cacThanhVien as $tv) {
			if($tv->getSex() === null) {
				$tenThanh = $tv->getTenThanh();
				if( ! empty($tenThanh)) {
					$tv->setSex($tenThanh->getSex());
					$manager->persist($tv);
				}
			}
			
			if($tv->isThieuNhi()) {
				if(empty($tv->isEnabled())) {
					if(empty($phanBoNamNay = $tv->getPhanBoNamNay())) {
						$output->writeln([ 'no need to fix for ' . $tv->getName() . ' nam hoc ' . $tv->getNamHoc() ]);
					};
					if($tv->getNamHoc() === 2018 || ! empty($phanBoNamNay)) {
//							$output->writeln([ 'fixing for ' . $tv->getName() . ' cd ' . $phanBoNamNay->getChiDoan()->getId() ]);
//						if($phanBoNamNay->getNamHoc()->getId() === 2018) {
						// fix data
//							$manager->remove($phanBoNamNay);
//						}
					}
				}
				
				if( ! empty($phanBoNamNay = $tv->getPhanBoNamNay())) {
					if($phanBoNamNay->isHuynhTruong()) {
						$output->writeln([
							'============================',
							'Truong is marked as ThieuNhi, needs fixing for ' . $tv->getName(),
							'================='
						]);
					}
				}
			}
			
			if( ! empty($phanBoNamNay = $tv->getPhanBoNamNay())) {
//				$output->writeln('Fixing Member Roles for ' . $tv->getName());
//				$phanBoNamNay->setVaiTro();
//				$manager->persist($phanBoNamNay);
			}
			/** @var PhanBo $phanBo */
//			foreach($tv->getPhanBoHangNam() as $phanBo) {
//				if(empty($phanBo->getNamHoc())) {
//					$phanBo->setNamHoc($phanBo->getChiDoan()->getNamHoc());
//					$output->writeln([
//						'phanBo ' . $tv->getName(),
//						' duoc gan vao namhoc ' . $phanBo->getChiDoan()->getNamHoc()->getId()
//					]);
//					$manager->persist($phanBo);
//
//				}
//
//				if($tv->isSoeur()) {
//					$phanBo->setSoeur(true);
//					$manager->persist($phanBo);
//				}
//
//				$manager->flush();
//			}

//			if( ! empty($cname = $tv->getChristianname())) {
//				$cname = mb_strtoupper(trim($cname));
//				try {
////					$output->writeln($tv->getName());
//					$enName = ThanhVien::$christianNames[ $cname ];
//					if(empty($tenThanh = $cNameRepo->findOneBy([ 'code' => $enName ]))) {
//						$tenThanh = new ChristianName();
//						$tenThanh->getCacThanhVien()->add($tv);
//						$tenThanh->setSex(ThanhVien::$christianNameSex[ $cname ]);
//						$tenThanh->setTiengViet($cname);
//						$tenThanh->setTiengAnh($enName);
//						$tenThanh->setCode($enName);
//						$manager->persist($tenThanh);
//						$manager->flush();
//					}
//					$tv->setTenThanh($tenThanh);
//					$manager->persist($tv);
//				} catch(ContextErrorException $ex) {
//					$output->writeln('ERROR ' . $ex->getTraceAsString());
//					$output->writeln($tv->getChristianname());
//					$output->writeln($tv->getName());
//					$output->writeln($cname . ' - ' . $enName);
//
//					$output->writeln(ThanhVien::$christianNames[ $cname ]);
////					var_dump(ThanhVien::$christianNameSex);
//					die(- 1);
//				}
//			}
		}

//		$cNameViet = array_flip(ThanhVien::$christianNames);
//		foreach($cNameViet as $cname) {
//			if( ! empty($cname)) {
//				$cname = mb_strtoupper(trim($cname));
//				try {
////					$output->writeln($tv->getName());
//					$enName = ThanhVien::$christianNames[ $cname ];
//					if(empty($tenThanh = $cNameRepo->findOneBy([ 'code' => $enName ]))) {
//						$tenThanh = new ChristianName();
//						$tenThanh->getCacThanhVien()->add($tv);
//						$tenThanh->setSex(ThanhVien::$christianNameSex[ $cname ]);
//						$tenThanh->setTiengViet($cname);
//						$tenThanh->setTiengAnh($enName);
//						$tenThanh->setCode($enName);
//						$manager->persist($tenThanh);
//						$manager->flush();
//					}
//				} catch(ContextErrorException $ex) {
//					$output->writeln('ERROR ' . $ex->getTraceAsString());
//					$output->writeln($cname . ' - ' . $enName);
//					$output->writeln(ThanhVien::$christianNames[ $cname ]);
////					var_dump(ThanhVien::$christianNameSex);
//					die(- 1);
//				}
//			}
//
//		}
		$output->writeln("Flushing");
		$manager->flush();
		$output->writeln("Successfully migrated all members.");
	}
}
