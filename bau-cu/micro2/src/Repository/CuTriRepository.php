<?php

namespace App\Repository;

use App\Entity\CuTri;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CuTri|null find($id, $lockMode = null, $lockVersion = null)
 * @method CuTri|null findOneBy(array $criteria, array $orderBy = null)
 * @method CuTri[]    findAll()
 * @method CuTri[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CuTriRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CuTri::class);
    }

    public function findByCuTriDaBauChoVong($vong, $year)
    {
        $alias = 'phieuBau';
        return $this->createQueryBuilder('c')
            ->join('c.cacPhienBau', $alias)
            ->andWhere('phieuBau.vong LIKE :val')
            ->andWhere('c.year LIKE :year')
            ->andWhere('c.submitted = :trueVal')
            ->setParameter('trueVal', true)
            ->setParameter('year', $year)
            ->setParameter('val', $vong)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(200)
            ->getQuery()
            ->getResult();
    }


    // /**
    //  * @return CuTri[] Returns an array of CuTri objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CuTri
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
