<?php

namespace App\Repository;

use App\Entity\InvoiceSupplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvoiceSupplier>
 *
 * @method InvoiceSupplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceSupplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceSupplier[]    findAll()
 * @method InvoiceSupplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceSupplierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceSupplier::class);
    }

    //    /**
    //     * @return InvoiceSupplier[] Returns an array of InvoiceSupplier objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?InvoiceSupplier
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
