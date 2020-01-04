<?php

namespace App\Repository;

use App\Entity\ViTri;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ViTri|null find($id, $lockMode = null, $lockVersion = null)
 * @method ViTri|null findOneBy(array $criteria, array $orderBy = null)
 * @method ViTri[]    findAll()
 * @method ViTri[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ViTriRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ViTri::class);
    }

    // /**
    //  * @return ViTri[] Returns an array of ViTri objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ViTri
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
