<?php

namespace App\Repository;

use App\Entity\ConferenceRoom;
use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for Reservation entity.
 * Implements the Repository pattern for database operations.
 *
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Save a reservation to the database.
     */
    public function save(Reservation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a reservation from the database.
     */
    public function remove(Reservation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Check if there are any overlapping reservations for a given room, date, and time range.
     * This is crucial for preventing double-booking.
     *
     * @param ConferenceRoom $room The conference room to check
     * @param \DateTimeInterface $date The date of the reservation
     * @param \DateTimeInterface $startTime The start time
     * @param \DateTimeInterface $endTime The end time
     * @param int|null $excludeReservationId Optional ID to exclude (for updates)
     * @return bool True if there's a conflict, false otherwise
     */
    public function hasOverlappingReservation(
        ConferenceRoom $room,
        \DateTimeInterface $date,
        \DateTimeInterface $startTime,
        \DateTimeInterface $endTime,
        ?int $excludeReservationId = null
    ): bool {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.conferenceRoom = :room')
            ->andWhere('r.date = :date')
            // Check for time overlap: new reservation overlaps if it starts before existing ends
            // AND ends after existing starts
            ->andWhere('r.startTime < :endTime')
            ->andWhere('r.endTime > :startTime')
            ->setParameter('room', $room)
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('startTime', $startTime->format('H:i:s'))
            ->setParameter('endTime', $endTime->format('H:i:s'));

        if ($excludeReservationId !== null) {
            $qb->andWhere('r.id != :excludeId')
               ->setParameter('excludeId', $excludeReservationId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Find all reservations for a specific room.
     *
     * @return Reservation[]
     */
    public function findByRoom(ConferenceRoom $room): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.conferenceRoom = :room')
            ->setParameter('room', $room)
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all reservations for a specific date.
     *
     * @return Reservation[]
     */
    public function findByDate(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('r.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all reservations within a date range.
     *
     * @return Reservation[]
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.date >= :startDate')
            ->andWhere('r.date <= :endDate')
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find reservations by reserver name (case-insensitive search).
     *
     * @return Reservation[]
     */
    public function findByReserverName(string $name): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('LOWER(r.reserverName) LIKE LOWER(:name)')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('r.date', 'DESC')
            ->addOrderBy('r.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get reservations for a room within a date range.
     *
     * @return Reservation[]
     */
    public function findByRoomAndDateRange(
        ConferenceRoom $room,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): array {
        return $this->createQueryBuilder('r')
            ->andWhere('r.conferenceRoom = :room')
            ->andWhere('r.date >= :startDate')
            ->andWhere('r.date <= :endDate')
            ->setParameter('room', $room)
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find reservations with optional filters.
     *
     * @return Reservation[]
     */
    public function findByFilters(
        ?int $roomId = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $date = null
    ): array {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.startTime', 'ASC');

        if ($roomId !== null) {
            $qb->andWhere('r.conferenceRoom = :roomId')
               ->setParameter('roomId', $roomId);
        }

        if ($date !== null) {
            $qb->andWhere('r.date = :date')
               ->setParameter('date', $date);
        } elseif ($startDate !== null && $endDate !== null) {
            $qb->andWhere('r.date >= :startDate')
               ->andWhere('r.date <= :endDate')
               ->setParameter('startDate', $startDate)
               ->setParameter('endDate', $endDate);
        }

        return $qb->getQuery()->getResult();
    }
}
