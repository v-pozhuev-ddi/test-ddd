<?php


namespace App\Ddd\Infrastructure\Address;

use App\Ddd\Domain\Address\Entity\Address;
use App\Ddd\Domain\Client\Entity\Client;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AddressRepository
 *
 * @package App\Ddd\Infrastructure\Address
 */
class AddressRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function createQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('a')->select('a');
    }

    /**
     * @param Client $client
     * @return QueryBuilder
     */
    public function createQueryFindByDefaultClientAddress(Client $client): QueryBuilder
    {
        return $this->createQuery()
            ->andWhere('a.client = :client')
            ->andWhere('a.isDefault = :isDefault')
            ->setParameters([
                'client' => $client,
                'isDefault' => true
            ]);
    }

    /**
     * @param Client $client
     * @return Address|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDefaultByClient(Client $client): ?Address
    {
        return $this->createQueryFindByDefaultClientAddress($client)->getQuery()->getOneOrNullResult();
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLast()
    {
        return $this->createQuery()
            ->setMaxResults(1)
            ->orderBy('a.id', 'DESC')
            ->getQuery()->getSingleResult();
    }

    /**
     * @param Client $client
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByClient(Client $client)
    {
        return $this->createQueryBuilder('a')->select('count(a.id)')
            ->andWhere('a.client = :client')
            ->setParameter('client', $client)
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneDefault()
    {
        return $this->createQuery()
            ->andWhere('a.isDefault = :isDefault')
            ->setParameter('isDefault', true)
            ->setMaxResults(1)
            ->orderBy('a.id', 'DESC')
            ->getQuery()->getSingleResult();
    }
}