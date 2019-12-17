<?php

namespace App\Repository;

use App\Entity\NhiemKy;
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
