<?php

namespace App\DataFixtures;

use App\Entity\ConferenceRoom;
use App\Entity\Reservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $rooms = [
            $this->getReference(ConferenceRoomFixtures::ROOM_INNOVATION_LAB, ConferenceRoom::class),
            $this->getReference(ConferenceRoomFixtures::ROOM_EXECUTIVE_SUITE, ConferenceRoom::class),
            $this->getReference(ConferenceRoomFixtures::ROOM_TRAINING_ROOM, ConferenceRoom::class),
            $this->getReference(ConferenceRoomFixtures::ROOM_QUICK_SYNC, ConferenceRoom::class),
            $this->getReference(ConferenceRoomFixtures::ROOM_BOARD_ROOM, ConferenceRoom::class),
        ];

        $reservers = ['John Smith', 'Emily Johnson', 'Michael Brown', 'Sarah Davis', 'David Wilson'];
        $meetingNotes = [
            'Team standup meeting',
            'Project review session',
            'Client call - Q1 planning',
            'Sprint planning',
            'Technical interview',
            'Workshop: New tools training',
        ];

        $today = new \DateTime('today');
        $monday = (clone $today)->modify('monday this week');

        $timeSlots = [
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:30', 'end' => '11:30'],
            ['start' => '13:00', 'end' => '14:00'],
            ['start' => '14:30', 'end' => '16:00'],
            ['start' => '16:30', 'end' => '17:30'],
        ];

        for ($day = 0; $day < 5; $day++) {
            $date = (clone $monday)->modify("+{$day} days");

            if ($date < $today) {
                continue;
            }

            $dailySlots = array_rand($timeSlots, 3);
            if (!is_array($dailySlots)) {
                $dailySlots = [$dailySlots];
            }

            foreach ($dailySlots as $index => $slotIndex) {
                $slot = $timeSlots[$slotIndex];
                $room = $rooms[($day + $index) % count($rooms)];
                $reserver = $reservers[array_rand($reservers)];
                $notes = $meetingNotes[array_rand($meetingNotes)];

                $startTime = \DateTime::createFromFormat('H:i', $slot['start']);
                $endTime = \DateTime::createFromFormat('H:i', $slot['end']);

                if ($date->format('Y-m-d') === $today->format('Y-m-d')) {
                    $now = new \DateTime();
                    $checkTime = (clone $date)->setTime((int)$startTime->format('H'), (int)$startTime->format('i'));
                    if ($checkTime <= $now) {
                        continue;
                    }
                }

                $reservation = new Reservation();
                $reservation->setConferenceRoom($room);
                $reservation->setReserverName($reserver);
                $reservation->setDate(clone $date);
                $reservation->setStartTime($startTime);
                $reservation->setEndTime($endTime);
                $reservation->setNotes($notes);

                $manager->persist($reservation);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ConferenceRoomFixtures::class,
        ];
    }
}
