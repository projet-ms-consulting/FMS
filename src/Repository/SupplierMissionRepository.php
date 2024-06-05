<?php

namespace App\Repository;

use App\Entity\SupplierMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SupplierMission>
 *
 * @method SupplierMission|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupplierMission|null findOneBy(array $criteria, array $orderBy = null)
 * @method SupplierMission[]    findAll()
 * @method SupplierMission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplierMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SupplierMission::class);
    }

    //    /**
    //     * @return SupplierMission[] Returns an array of SupplierMission objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

//        public function findOneBySomeField($value): ?SupplierMission
//        {
//            return $this->createQueryBuilder('s')
//                ->andWhere('s.exampleField = :val')
//                ->setParameter('val', $value)
//                ->getQuery()
//                ->getOneOrNullResult()
//            ;
//        }
}
