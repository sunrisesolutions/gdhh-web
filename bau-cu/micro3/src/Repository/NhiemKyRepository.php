<?php

namespace App\Repository;

use App\Entity\HuynhTruong;
use App\Entity\HuynhTruongXinRut;
use App\Entity\NhiemKy;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NhiemKy|null find($id, $lockMode = null, $lockVersion = null)
 * @method NhiemKy|null findOneBy(array $criteria, array $orderBy = null)
 * @method NhiemKy[]    findAll()
 * @method NhiemKy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NhiemKyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NhiemKy::class);
    }

    public function getDanhSachUngCu(NhiemKy $nhiemKy, int $vong)
    {
        $year = $nhiemKy->getYear();
        if ($vong > 1) {
            $vongTruoc = $vong - 1;
            $quyDinhTop = $nhiemKy->{'getTopVong'.$vongTruoc}();

            $topVong1 = $this->getEntityManager()->getRepository(HuynhTruong::class)->findBy(['enabled' => true, 'year' => $year], ['vong'.$vongTruoc => 'DESC', 'vong'.$vongTruoc.'phu' => 'DESC'], $quyDinhTop);
            $conLai = $this->getEntityManager()->getRepository(HuynhTruong::class)->findBy(['enabled' => true, 'year' => $year], ['vong'.$vongTruoc => 'DESC'], null, $nhiemKy->{'getTopVong'.$vongTruoc}());

            /** @var HuynhTruong $cuoiTopVong1 */
            $cuoiTopVong1 = end($topVong1);
            if (count($conLai) > 0) {
                /** @var HuynhTruong $dauConLai */
                $dauConLai = array_slice($conLai, 0, 1)[0];
                $dsPhu = [];

                if ($nhiemKy->getVong1phu() || $nhiemKy->getVong1phu() === null) {
                    while ($cuoiTopVong1->getVong1() === $dauConLai->getVong1()) {
                        $topVong1[] = $cuoiTopVong1 = array_shift($conLai);
                        /** @var HuynhTruong $dauConLai */
                        $dauConLai = array_slice($conLai, 0, 1)[0];
                    }
                }

                if ($nhiemKy->getVong1phu() !== null) {
                    if (count($topVong1) > $quyDinhTop) {
                        /** @var HuynhTruong $cuoiTopVong1 */
                        $cuoiTopVong1 = array_pop($topVong1);
                        /** @var HuynhTruong $cuoiTopVong1 */
                        $keCuoiTopVong1 = end($topVong1);

                        $topVong1[] = $cuoiTopVong1;

                        while ($cuoiTopVong1->getVong1() === $keCuoiTopVong1->getVong1()) {
                            /** @var HuynhTruong $cuoiTopVong1 */
                            $dsPhu[] = $cuoiTopVong1 = array_pop($topVong1);
                            /** @var HuynhTruong $cuoiTopVong1 */
                            $keCuoiTopVong1 = end($topVong1);
                        }
                        if (count($dsPhu) === 0) {
                            $topVong1[] = $cuoiTopVong1;
                        }
                    }
                }
            }

            $truong = $topVong1;
            usort($truong, function ($a, $b) {
                $s = Slugify::create();
                return strcmp($s->slugify($a->getFirstName()), $s->slugify($b->getFirstName()));
            });

        } else {
            $truong = $this->getEntityManager()->getRepository(HuynhTruong::class)->findBy([], ['firstName' => 'ASC'], $nhiemKy->{'getTopVong'.$vong}());
        }
        return $truong;
    }

    public function getKetQuaBauCu($year, $vong)
    {
        /** @var NhiemKy $nhiemKy */
        $nhiemKy = $this->getEntityManager()->getRepository(NhiemKy::class)->findOneByYear($year);

        $quyDinhTop = $nhiemKy->{'getTopVong'.$vong}();

        //  $vong === 1 ? null : $nhiemKy->{'getTopVong'.($vong - 1)}() - count($topVong1)

        $topVong1 = $this->getEntityManager()->getRepository(HuynhTruong::class)->findBy(['enabled' => true, 'year' => $year], ['vong'.$vong => 'DESC', 'vong'.$vong.'phu' => 'DESC'], $quyDinhTop);

        $dsTruongUngCu = $this->getDanhSachUngCu($nhiemKy, $vong);

        $conLai = $this->getEntityManager()->getRepository(HuynhTruong::class)->findBy(['enabled' => true, 'year' => $year], ['vong'.$vong => 'DESC'], null, $quyDinhTop);
        $dsRut = $this->getEntityManager()->getRepository(HuynhTruongXinRut::class)->findBy(['year' => $year, 'vong' => $vong]);

        /** @var HuynhTruong $cuoiTopVong1 */
        $cuoiTopVong1 = end($topVong1);

        /** @var HuynhTruong $truongConLai */
        foreach ($conLai as $key => $truongConLai) {
            $notFound = true;
            foreach ($dsTruongUngCu as $truong) {
                if ($truongConLai->getName() === $truong->getName()) {
                    $notFound = false;
                    break;
                };
            }
            if ($notFound || empty($truongConLai->getEnabled())) {
                unset($conLai[$key]);
            }
        }

        /** @var HuynhTruong $truongConLai */
        foreach ($topVong1 as $key => $truongConLai) {
            $notFound = true;
            foreach ($dsTruongUngCu as $truong) {
                if ($truongConLai->getName() === $truong->getName()) {
                    $notFound = false;
                    break;
                };
            }
            if ($notFound || empty($truongConLai->getEnabled())) {
                unset($topVong1[$key]);
            }
        }

        while (!empty($cuoiTopVong1) && $cuoiTopVong1->{'getVong'.$vong}() == 0) {
            $conLai[] = array_pop($topVong1);
            /** @var HuynhTruong $cuoiTopVong1 */
            $cuoiTopVong1 = end($topVong1);
        }

        $dsPhu = [];

        if (count($conLai) > 0) {
            /** @var HuynhTruong $dauConLai */
            $dauConLai = array_slice($conLai, 0, 1)[0];

            if ($nhiemKy->{'getVong'.$vong.'phu'}() || $nhiemKy->{'getVong'.$vong.'phu'}() === null) {
                while (!empty($cuoiTopVong1) && $cuoiTopVong1->{'getVong'.$vong}() === $dauConLai->{'getVong'.$vong}()) {
                    $topVong1[] = $cuoiTopVong1 = array_shift($conLai);
                    if (count($conLai) === 0) {
                        break;
                    }
                    /** @var HuynhTruong $dauConLai */
                    $dauConLai = array_slice($conLai, 0, 1)[0];
                }
            }

            if ($nhiemKy->{'getVong'.$vong.'phu'}()) {
                if (count($topVong1) > $quyDinhTop) {
                    /** @var HuynhTruong $cuoiTopVong1 */
                    $cuoiTopVong1 = array_pop($topVong1);
                    /** @var HuynhTruong $cuoiTopVong1 */
                    $keCuoiTopVong1 = end($topVong1);

                    $topVong1[] = $cuoiTopVong1;

                    while ($cuoiTopVong1->{'getVong'.$vong}() === $keCuoiTopVong1->{'getVong'.$vong}()) {
                        /** @var HuynhTruong $cuoiTopVong1 */
                        $dsPhu[] = $cuoiTopVong1 = array_pop($topVong1);
                        /** @var HuynhTruong $cuoiTopVong1 */
                        $keCuoiTopVong1 = end($topVong1);
                    }
                    if (count($dsPhu) === 0) {
                        $topVong1[] = $cuoiTopVong1;
                    }
                }
            }
        }

        return ['top' => $topVong1, 'conLai' => $conLai,
            'rut' => $dsRut,
            'phu' => $dsPhu
        ];
    }

    // /**
    //  * @return NhiemKy[] Returns an array of NhiemKy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NhiemKy
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
