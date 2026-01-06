<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use App\Validator\EndTimeAfterStartTime;
use App\Validator\NoOverlappingReservation;
use App\Validator\NotInPast;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity representing a reservation for a conference room.
 */
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservations')]
#[ORM\HasLifecycleCallbacks]
#[EndTimeAfterStartTime]
#[NoOverlappingReservation]
#[NotInPast]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConferenceRoom $conferenceRoom = null;

    #[ORM\Column(length: 255)]
    private ?string $reserverName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConferenceRoom(): ?ConferenceRoom
    {
        return $this->conferenceRoom;
    }

    public function setConferenceRoom(?ConferenceRoom $conferenceRoom): static
    {
        $this->conferenceRoom = $conferenceRoom;
        return $this;
    }

    public function getReserverName(): ?string
    {
        return $this->reserverName;
    }

    public function setReserverName(string $reserverName): static
    {
        $this->reserverName = $reserverName;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Convert entity to array for JSON serialization.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'conferenceRoom' => $this->conferenceRoom?->getId(),
            'conferenceRoomName' => $this->conferenceRoom?->getName(),
            'reserverName' => $this->reserverName,
            'date' => $this->date?->format('Y-m-d'),
            'startTime' => $this->startTime?->format('H:i'),
            'endTime' => $this->endTime?->format('H:i'),
            'notes' => $this->notes,
            'createdAt' => $this->createdAt?->format('c'),
            'updatedAt' => $this->updatedAt?->format('c'),
        ];
    }
}
