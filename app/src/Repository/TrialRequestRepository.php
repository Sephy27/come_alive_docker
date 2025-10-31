<?php

namespace App\Repository;

use App\Entity\TrialRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TrialRequest>
 * @method TrialRequest[]   findAll()
 */
class TrialRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrialRequest::class);
    }

   /**
     * @return TrialRequest[] Returns an array of TrialRequest objects
     */
    //public function findTrialRequest(string $name): array
    //{
    //    return $this->createQueryBuilder('r')
    //        ->Where('r.name <= :name')
    //        ->setParameter('name', $name)
    //        ->orderBy('r.name', 'ASC')    
    //        ->setMaxResults(10)        
    //        ->getQuery()
    //        ->getResult()
    //    ;
    //}

//    public function findOneBySomeField($value): ?TrialRequest
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}