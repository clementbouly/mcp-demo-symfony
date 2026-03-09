<?php

namespace App\Repository;

use App\Entity\GroceryList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroceryList>
 */
class GroceryListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroceryList::class);
    }

    /** @return GroceryList[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getNextPosition(): int
    {
        $result = $this->createQueryBuilder('l')
            ->select('MAX(l.position)')
            ->getQuery()
            ->getSingleScalarResult();

        return ($result ?? -1) + 1;
    }
}
