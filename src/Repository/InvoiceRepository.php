<?php

namespace App\Repository;

use App\Entity\InvoiceMission;
use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<InvoiceMission>
 *
 * @method InvoiceMission|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceMission|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceMission[]    findAll()
 * @method InvoiceMission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, InvoiceMission::class);
    }

    public function paginateinvoices(int $page, int $limit, Mission $mission): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('i')
                ->leftJoin('i.mission', 'm')
                ->where('m.id = :missionId')
                ->setParameter('missionId', $mission->getId())
                ->select('m', 'i'),
            $page,
            $limit,
            [
                'defaultSortFieldName' => 'm.id',
                'defaultSortDirection' => 'asc',
            ]
        );
    }
}
