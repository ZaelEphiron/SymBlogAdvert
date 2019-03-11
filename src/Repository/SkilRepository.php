<?php

namespace App\Repository;

use App\Entity\Skil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Skil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skil[]    findAll()
 * @method Skil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkilRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Skil::class);
    }

    // /**
    //  * @return Skil[] Returns an array of Skil objects
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
    public function findOneBySomeField($value): ?Skil
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
