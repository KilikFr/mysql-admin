<?php

namespace App\Repository;

use App\Entity\Slave;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Slave|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slave|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slave[]    findAll()
 * @method Slave[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlaveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slave::class);
    }

    // /**
    //  * @return Slave[] Returns an array of Slave objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Slave
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
