<?php

namespace App\DataFixtures;

use App\Entity\ViTri;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class VitriFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $year = '2020-2024';

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Thủ quỹ Xứ Đoàn');
        $manager->persist($v);

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Thư ký Xứ Đoàn');
        $manager->persist($v);

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Phân đoàn Trưởng Chiên Con');
        $manager->persist($v);

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Phân đoàn Trưởng Ấu Nhi');
        $manager->persist($v);

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Phân đoàn Trưởng Thiếu Nhi');
        $manager->persist($v);

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Phân đoàn Trưởng Nghĩa Sỹ');
        $manager->persist($v);

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Phân đoàn Trưởng Hiệp Sỹ');
        $manager->persist($v);

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Trưởng Ban Phụng Vụ');
        $manager->persist($v);

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Trưởng Ban Văn Thể Mỹ');
        $manager->persist($v);

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Trưởng Ban Kỷ Luật');
        $manager->persist($v);

        $v = new ViTri();
        $v->setYear($year);
        $v->setName('Trưởng Ban Đời Sống');
        $manager->persist($v);

        $manager->flush();
    }
}
