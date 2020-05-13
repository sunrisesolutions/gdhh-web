<?php

namespace App\Command;

use App\Entity\HuynhTruong;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateTruongCommand extends Command
{
    const FIELD_CHRISTIAN_NAME = 'CNAME';
    const FIELD_NAME = 'NAME';
    const FIELD_AKA = 'AKA';
    const FIELD_DOB = 'DOB';
    const FIELD_PD = 'PD';
    const FIELD_CD = 'CD';
    const FIELD_FIRST = 'FNAME';

    const NGHIA = 'NGHIA';
    const TONGDO = 'TONGDO';

    const dsTruong = [
        [
            'id' => 1097,
            self::FIELD_CHRISTIAN_NAME => 'MARIA',
            self::FIELD_FIRST => 'TRÂM',
            self::FIELD_NAME => 'MARIA NGUYỄN THỊ HUYỀN TRÂM',
            self::FIELD_AKA => 'HUYỀN TRÂM',
            self::FIELD_DOB => 0,
            self::FIELD_PD => 'Ấu Nhi (ANB)',
            self::FIELD_CD => '09',
        ],
        [
            'id' => 1123,
            self::FIELD_CHRISTIAN_NAME => 'TERESA',
            self::FIELD_FIRST => 'OANH',
            self::FIELD_NAME => 'TERESA HOÀNG OANH',
            self::FIELD_AKA => 'HOÀNG OANH',
            self::FIELD_DOB => 1987,
            self::FIELD_PD => 'Ấu Nhi (ANB)',
            self::FIELD_CD => '09',
        ],
        [
            'id' => 1040,
            self::FIELD_CHRISTIAN_NAME => 'PHANXICO-ASSISI',
            self::FIELD_FIRST => 'LONG',
            self::FIELD_NAME => 'PHANXICO-ASSISI TRẦN ĐỨC LONG',
            self::FIELD_AKA => 'ĐỨC LONG',
            self::FIELD_DOB => 1984,
            self::FIELD_PD => 'Thiếu Nhi (TNB)',
            self::FIELD_CD => 0,
        ],
        [
            'id' => 1031,
            self::FIELD_CHRISTIAN_NAME => 'MARIA',
            self::FIELD_FIRST => 'LOAN',
            self::FIELD_NAME => 'MARIA NGUYỄN NGỌC HỒNG LOAN',
            self::FIELD_AKA => 'HỒNG LOAN',
            self::FIELD_DOB => 1986,
            self::FIELD_PD => 'Thiếu Nhi (TNB)',
            self::FIELD_CD => 11,
        ],
        [
            'id' => 1059,
            self::FIELD_CHRISTIAN_NAME => 'ANTON',
            self::FIELD_FIRST => 'KHÁNH',
            self::FIELD_NAME => 'ANTON HOÀNG MINH KHÁNH',
            self::FIELD_AKA => 'MINH KHÁNH',
            self::FIELD_DOB => 1993,
            self::FIELD_PD => 'Thiếu Nhi (TNB)',
            self::FIELD_CD => 11,
        ],
        [
            'id' => 1032,
            self::FIELD_CHRISTIAN_NAME => 'MARIA',
            self::FIELD_FIRST => 'HÀ',
            self::FIELD_NAME => 'MARIA TRẦN THỊ NGỌC HÀ',
            self::FIELD_AKA => 'NGỌC HÀ',
            self::FIELD_DOB => 1976,
            self::FIELD_PD => 'Ấu Nhi (ANB)',
            self::FIELD_CD => '09',
        ],
        [
            'id' => 949,
            self::FIELD_CHRISTIAN_NAME => 'GIUSE',
            self::FIELD_FIRST => 'DUY',
            self::FIELD_NAME => 'GIUSE BÙI ĐỨC DUY',
            self::FIELD_AKA => 'ĐỨC DUY',
            self::FIELD_DOB => 1982,
            self::FIELD_PD => 'Tông Đồ/ Hiệp sỹ (HSB)',
            self::FIELD_CD => 18,
        ],
        [
            'id' => 1068,
            self::FIELD_CHRISTIAN_NAME => 'ANNA',
            self::FIELD_FIRST => 'AN',
            self::FIELD_NAME => 'ANNA ĐẶNG THUỲ AN',
            self::FIELD_AKA => 'THUỲ AN',
            self::FIELD_DOB => 1993,
            self::FIELD_PD => 'Nghĩa Sĩ (NSB)',
            self::FIELD_CD => 15,
        ],
        [
            'id' => 1055,
            self::FIELD_CHRISTIAN_NAME => 'MARIA',
            self::FIELD_FIRST => 'ANH',
            self::FIELD_NAME => 'MARIA NGUYỄN NGỌC ANH',
            self::FIELD_AKA => 'NGỌC ANH',
            self::FIELD_DOB => 1989,
            self::FIELD_PD => 'Thiếu Nhi (TNB)',
            self::FIELD_CD => 12,
        ],
        [
            'id' => 650,
            self::FIELD_CHRISTIAN_NAME => 'GIUSE',
            self::FIELD_FIRST => 'ĐẠI',
            self::FIELD_NAME => 'GIUSE NGUYỄN QUANG ĐẠI',
            self::FIELD_AKA => 'QUANG ĐẠI',
            self::FIELD_DOB => 1989,
            self::FIELD_PD => 'Ấu Nhi (ANB)',
            self::FIELD_CD => 0,
        ],



//        [
//            'id' => 1077,
//            self::FIELD_CHRISTIAN_NAME => 'GIUSE',
//            self::FIELD_FIRST => 'HIẾU',
//            self::FIELD_NAME => 'GIUSE TỪ TRUNG HIẾU',
//            self::FIELD_AKA => 'TRUNG HIẾU',
//            self::FIELD_DOB => 1984,
//            self::FIELD_PD => 'Chiên Con (CCB)',
//            self::FIELD_CD => '06',
//        ],

    ];

    protected static $defaultName = 'app:generate:truong';

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

        $truongs = $this->em->getRepository(HuynhTruong::class)->findAll();
        foreach ($truongs as $truong) {
//            $this->em->remove($truong);
        }
//        $this->em->flush();

        foreach (self::dsTruong as $data) {
            $tr = new HuynhTruong();
            $tr->setName($data[self::FIELD_NAME]);
            $tr
                ->setFirstName($data[self::FIELD_FIRST])
                ->setEnabled(true)
                ->setAka($data[self::FIELD_AKA])
                ->setChristianName($data[self::FIELD_CHRISTIAN_NAME])
                ->setChiDoan($data[self::FIELD_CD])
                ->setPhanDoan($data[self::FIELD_PD])
                ->setDob($data[self::FIELD_DOB]);
//            $this->em->persist($tr);
        }
//        $this->em->flush();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
