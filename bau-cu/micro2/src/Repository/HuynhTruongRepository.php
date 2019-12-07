<?php

namespace App\Repository;

use App\Entity\HuynhTruong;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HuynhTruong|null find($id, $lockMode = null, $lockVersion = null)
 * @method HuynhTruong|null findOneBy(array $criteria, array $orderBy = null)
 * @method HuynhTruong[]    findAll()
 * @method HuynhTruong[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HuynhTruongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HuynhTruong::class);
    }

    // /**
    //  * @return HuynhTruong[] Returns an array of HuynhTruong objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HuynhTruong
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
