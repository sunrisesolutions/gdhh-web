<?php

namespace App\Repository;

use App\Entity\HuynhTruongXinRut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HuynhTruongXinRut|null find($id, $lockMode = null, $lockVersion = null)
 * @method HuynhTruongXinRut|null findOneBy(array $criteria, array $orderBy = null)
 * @method HuynhTruongXinRut[]    findAll()
 * @method HuynhTruongXinRut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HuynhTruongXinRutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HuynhTruongXinRut::class);
    }

    // /**
    //  * @return HuynhTruongXinRut[] Returns an array of HuynhTruongXinRut objects
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
    public function findOneBySomeField($value): ?HuynhTruongXinRut
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
