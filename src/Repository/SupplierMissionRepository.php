<?php

namespace App\Repository;

use App\Entity\SupplierMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

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
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, SupplierMission::class);
    }

    public function paginateSupplierMissions(int $page, int $limit): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('s')
                ->leftJoin('s.supplier', 'c')
                ->select('s', 'c'),
            $page,
            $limit,
            [
                'defaultSortFieldName' => 's.id',
                'defaultSortDirection' => 'asc',
            ]
        );
    }
}
