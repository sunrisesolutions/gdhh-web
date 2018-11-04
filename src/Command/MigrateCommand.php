<?php

namespace App\Command;

use App\Entity\Backup\PhanBo180825;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\ChristianName;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:migrate')
            // the short description shown while running "php bin/console list"
            ->setDescription('Migrate CNames and Missing phanbo in ThanhVien')
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
        $cacPhanBo = $pbRepo->findAll();
//		$pb180825Repo    = $this->getContainer()->get('doctrine')->getRepository(PhanBo180825::class);
//		$cacPhanBo180825 = $pb180825Repo->findAll();
        $cdRepo = $this->getContainer()->get('doctrine')->getRepository(ChiDoan::class);

        $cacPhanBo180825Array = [];

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
        foreach ($cacPhanBo as $pb) {
//            if (false && $pb->getNamHoc()->getId() < 2018) {
//                $output->writeln('browsing PB < 2018');
//                if (empty($pb->getChiDoan())) {
//                    $output->writeln($pb->getThanhVien()->getId() . ' ' . $pb->getThanhVien()->getName() . ' has no Chi Doan Data for ' . $pb->getNamHoc()->getId());
//                } else if ($pb->getChiDoan()->getNamHoc() !== $pb->getNamHoc()) {
//                    $output->writeln($pb->getThanhVien()->getId() . ' ' . $pb->getThanhVien()->getName() . ' PB and CD have different NamHoc');
////					$pb->setChiDoan($cacPhanBo180825Array[ $pb->getId() ]);
////					$manager->persist($pb);
//                }
//                if ($pb->getChiDoan() !== $cacPhanBo180825Array[$pb->getId()] && !empty($cacPhanBo180825Array[$pb->getId()])) {
//                    $output->writeln($pb->getThanhVien()->getId() . ' ' . $pb->getThanhVien()->getName() . ' PB has different backup');
////					$pb->setChiDoan($cacPhanBo180825Array[ $pb->getId() ]);
////					$manager->persist($pb);
//                }
//            } else {
//                if (!empty($dngl = $pb->getDoiNhomGiaoLy())) {
//                    if ($dngl->getChiDoan() !== $pb->getChiDoan()) {
//                        $output->writeln("Fixing Chi doan data using DNGL info for " . $pb->getThanhVien()->getName());
//                        $pb->setChiDoan($dngl->getChiDoan());
//                        $manager->persist($pb);
//                    }
//                } elseif (!empty($pb->getChiDoan())) {
//                    $incorrectChiDoanNumber = $pb->getChiDoan()->getNumber();
//                    $cdNumber = $pb->getThanhVien()->getChiDoan();
//                    if ($cdNumber !== $incorrectChiDoanNumber || $pb->getChiDoan()->getNamHoc()->getId() !== 2018) {
//                        $cd = $cdRepo->findOneBy(['number' => $cdNumber, 'namHoc' => 2018]);
//                        if (!empty($cd)) {
//                            $output->writeln(['fixing Chidoan Data using cdNumber in ThanhVien Entity for ' . $pb->getThanhVien()->getName() . ' set CD to ' . $cdNumber . '-2018', '-------']);
//                            $pb->setChiDoan($cd);
//                            $manager->persist($pb);
//                        } else {
//                            if ($pb->getThanhVien()->isThieuNhi()) {
//                                $output->writeln('cannot find cd ' . $cdNumber . '-2018 for ' . $pb->getThanhVien()->getName());
//                            } else {
//                                $output->writeln($pb->getThanhVien()->getName() . ' is not a ThieuNhi, so no need to fix ChiDoan data');
//                            }
//                        }
//                    } else {
//
//                    }
//                }
//            }
        }
        $namHoc2016 = $this->getContainer()->get('doctrine')->getRepository(NamHoc::class)->find(2016);
        $cacPhanBo2016 = $pbRepo->findBy(['namHoc' => 2016]);
        /** @var PhanBo $pb2016 */
        foreach ($cacPhanBo2016 as $pb2016) {
            if ($pb2016->getChiDoan()->getNamHoc()->getId() === 2018) {
                if (empty($pb2017 = $pb2016->getPhanBoSau())) {
                    $output->writeln('Ko co phanbosau vi nghi ngay tu 2016, su dung data tu thanh vien' . $pb2016->getId() . ' ::: ' . $pb2016->getThanhVien()->getName());
                    $cd2017 = $pb2016->getThanhVien()->getChiDoan();
                } else {
                    $output->writeln('Syncing for ' . $pb2016->getId() . ' ::: ' . $pb2016->getThanhVien()->getName());
                    $cd2017 = $pb2017->getChiDoan()->getNumber();
                }
                $cd2016 = $cd2017 - 1;
                $cd2016Id = $cd2016 . '-2016';
                $pb2016->setChiDoan($cdRepo->find($cd2016Id));
                $manager->persist($pb2016);
            }
        }

        ///////////////

        /** @var ThanhVien $tv */
        foreach ($cacThanhVien as $tv) {
            if ($tv->getSex() === null) {
                $tenThanh = $tv->getTenThanh();
                if (!empty($tenThanh)) {
                    $tv->setSex($tenThanh->getSex());
                    $manager->persist($tv);
                }
            }

            if ($tv->isThieuNhi()) {
                if (empty($tv->isEnabled())) {
                    if (empty($phanBoNamNay = $tv->getPhanBoNamNay())) {
//						$output->writeln([ 'no need to fix for ' . $tv->getName() . ' nam hoc ' . $tv->getNamHoc() ]);
                    };
                    if ($tv->getNamHoc() === 2018 || !empty($phanBoNamNay)) {
//							$output->writeln([ 'fixing for ' . $tv->getName() . ' cd ' . $phanBoNamNay->getChiDoan()->getId() ]);
//						if($phanBoNamNay->getNamHoc()->getId() === 2018) {
                        // fix data
//							$manager->remove($phanBoNamNay);
//						}
                    }
                }

                if (!empty($phanBoNamNay = $tv->getPhanBoNamNay())) {
                    if ($phanBoNamNay->isHuynhTruong()) {
                        $output->writeln([
                            '============================',
                            'Truong is marked as ThieuNhi, needs fixing for ' . $tv->getName(),
                            '================='
                        ]);
                    }
                }
            }

            if (empty($tv->getQuickName())) {
                $output->writeln($tv->getName() . '... Quickname is null, update it now');
                $qn = $tv->getFirstname();
                if (empty($tv->getMiddlename())) {
                    $qn = $tv->getLastname() . ' ' . $tv->getFirstname();
                } else {
                    $middleNameArray = explode(' ', $tv->getMiddlename());
                    $lastMiddleName = array_pop($middleNameArray);
                    $qn = $lastMiddleName . ' ' . $qn;
                }

                $output->writeln('... ... peristing tv with updated QuickName ' . $qn);
                $tv->setQuickName($qn);
                $manager->persist($tv);
            }

            if (!empty($phanBoNamNay = $tv->getPhanBoNamNay())) {
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
