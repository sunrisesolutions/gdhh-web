<?php

namespace App\Command;

use App\Entity\Backup\PhanBo180825;
use App\Entity\HocBa\HienDien;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\ChristianName;
use App\Entity\HoSo\DiemChuyenCan;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateOutputCommand extends ContainerAwareCommand
{
    private $em;

    public function __construct(string $name = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->em = $entityManager;
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:generate-output')
            // the short description shown while running "php bin/console list"
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to migrate cnames of all Members...');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->em->getRepository(ThanhVien::class);
        $thanhVien = $repo->findBy(['enabled' => true, 'huynhTruong' => true, 'soeur' => false]);

        $output->writeln(count($thanhVien));

        /** @var ThanhVien $tv */
        foreach ($thanhVien as $tv) {
            $output
                ->writeln('[');
            $output
                ->writeln(sprintf("'id' => %s,", $tv->getId()));
            $output
                ->writeln(sprintf("self::FIELD_CHRISTIAN_NAME => '%s',", $tv->getChristianname()));
            $output
                ->writeln(sprintf("self::FIELD_FIRST => '%s',", $tv->getFirstname()));
            $output
                ->writeln(sprintf("self::FIELD_NAME => '%s',", $tv->getName()));
            $output
                ->writeln(sprintf("self::FIELD_AKA => '%s',", $tv->getQuickName()));
            $output
                ->writeln(sprintf("self::FIELD_DOB => %d,", $tv->getDob() ? $tv->getDob()->format('Y') : ''));
            $output
                ->writeln(sprintf("self::FIELD_PD => '%s',", $tv->getPhanDoan() === 'PHAN_DOAN_CHIEN' ? 'Chiên Con' : ($tv->getPhanDoan() === 'PHAN_DOAN_AU' ? 'Ấu Nhi' : ($tv->getPhanDoan() === 'PHAN_DOAN_THIEU' ? 'Thiếu Nhi' : ($tv->getPhanDoan() === 'PHAN_DOAN_NGHIA' ? 'Nghĩa Sĩ' : 'Tông Đồ/ Hiệp sỹ')))));
            $output
                ->writeln(sprintf("self::FIELD_CD => %d,", $tv->getChiDoan()));
            $output
                ->writeln(sprintf("],"));
        }
    }
}