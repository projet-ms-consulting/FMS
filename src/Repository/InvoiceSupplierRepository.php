<?php

namespace App\Repository;

use App\Entity\InvoiceSupplier;
use App\Entity\SupplierMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

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
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, InvoiceSupplier::class);
    }

    public function paginateinvoices(int $page, int $limit, SupplierMission $supplierMission): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('s')
                ->leftJoin('s.supplierMission', 'm')
                ->leftJoin('s.invoiceMission', 'i')
                ->where('m.id = :missionId')
                ->setParameter('missionId', $supplierMission->getId())
                ->select('m', 's', 'i'),
            $page,
            $limit,
            [
                'defaultSortFieldName' => 'm.id',
                'defaultSortDirection' => 'asc',
            ]
        );
    }
}
