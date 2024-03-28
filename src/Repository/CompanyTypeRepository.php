<?php

namespace App\Repository;

use App\Entity\TypeCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeCompany>
 *
 * @method TypeCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeCompany[]    findAll()
 * @method TypeCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeCompany::class);
    }

    //    /**
    //     * @return TypeCompany[] Returns an array of TypeCompany objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TypeCompany
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
