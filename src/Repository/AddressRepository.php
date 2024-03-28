<?php

namespace App\Repository;

use App\Entity\Address;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Address>
 *
 * @method Address|null find($id, $lockMode = null, $lockVersion = null)
 * @method Address|null findOneBy(array $criteria, array $orderBy = null)
 * @method Address[]    findAll()
 * @method Address[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Address::class);
    }

    public function paginateAdresses(int $page, int $limit): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('a')
            ->leftJoin('a.company', 'c')
            ->select('a', 'c'),
            $page,
            $limit,
            [
                'defaultSortFieldName' => 'a.id',
                'defaultSortDirection' => 'asc',
                'sortFieldWhitelist' => ['a.id', 'a.nbStr               eet', 'a.street', 'a.city', 'a.zipCode'],
            ]
        );
    }
}
