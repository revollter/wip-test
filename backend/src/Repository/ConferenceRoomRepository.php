<?php

namespace App\Repository;

use App\Entity\ConferenceRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for ConferenceRoom entity.
 * Implements the Repository pattern for database operations.
 *
 * @extends ServiceEntityRepository<ConferenceRoom>
 *
 * @method ConferenceRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConferenceRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConferenceRoom[]    findAll()
 * @method ConferenceRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConferenceRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConferenceRoom::class);
    }

    /**
     * Save a conference room to the database.
     */
    public function save(ConferenceRoom $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a conference room from the database.
     */
    public function remove(ConferenceRoom $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find all rooms ordered by name.
     *
     * @return ConferenceRoom[]
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find rooms by minimum capacity.
     *
     * @return ConferenceRoom[]
     */
    public function findByMinimumCapacity(int $minCapacity): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.capacity >= :minCapacity')
            ->setParameter('minCapacity', $minCapacity)
            ->orderBy('r.capacity', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search rooms by name (case-insensitive).
     *
     * @return ConferenceRoom[]
     */
    public function searchByName(string $searchTerm): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('LOWER(r.name) LIKE LOWER(:searchTerm)')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
