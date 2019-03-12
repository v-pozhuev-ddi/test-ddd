<?php


namespace App\Ddd\Infrastructure\Client;

use App\Ddd\Domain\Client\Entity\Client;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AddressRepository
 *
 * @package App\Ddd\Infrastructure\Address
 */
class ClientRepository extends EntityRepository
{

    public function createQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->select('c');
    }

    public function findLast()
    {
        return $this->createQuery()
            ->setMaxResults(1)
            ->orderBy('c.id', 'DESC')
            ->getQuery()->getSingleResult();
    }
}