<?php

namespace App\Repository;

use App\Entity\PhieuBau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PhieuBau|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhieuBau|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhieuBau[]    findAll()
 * @method PhieuBau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhieuBauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhieuBau::class);
    }

    // /**
    //  * @return PhieuBau[] Returns an array of PhieuBau objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PhieuBau
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
