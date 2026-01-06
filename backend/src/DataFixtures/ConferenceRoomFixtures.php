<?php

namespace App\DataFixtures;

use App\Entity\ConferenceRoom;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConferenceRoomFixtures extends Fixture
{
    public const ROOM_INNOVATION_LAB = 'room-innovation-lab';
    public const ROOM_EXECUTIVE_SUITE = 'room-executive-suite';
    public const ROOM_TRAINING_ROOM = 'room-training-room';
    public const ROOM_QUICK_SYNC = 'room-quick-sync';
    public const ROOM_BOARD_ROOM = 'room-board-room';

    public function load(ObjectManager $manager): void
    {
        $roomsData = [
            [
                'reference' => self::ROOM_INNOVATION_LAB,
                'name' => 'Innovation Lab',
                'description' => 'Creative space with whiteboards and brainstorming tools',
                'capacity' => 8,
                'location' => 'Floor 1, Room 101',
            ],
            [
                'reference' => self::ROOM_EXECUTIVE_SUITE,
                'name' => 'Executive Suite',
                'description' => 'Premium meeting room with video conferencing',
                'capacity' => 12,
                'location' => 'Floor 3, Room 301',
            ],
            [
                'reference' => self::ROOM_TRAINING_ROOM,
                'name' => 'Training Room',
                'description' => 'Large room suitable for workshops and training sessions',
                'capacity' => 25,
                'location' => 'Floor 2, Room 201',
            ],
            [
                'reference' => self::ROOM_QUICK_SYNC,
                'name' => 'Quick Sync',
                'description' => 'Small huddle room for quick meetings',
                'capacity' => 4,
                'location' => 'Floor 1, Room 102',
            ],
            [
                'reference' => self::ROOM_BOARD_ROOM,
                'name' => 'Board Room',
                'description' => 'Formal meeting room for board meetings and presentations',
                'capacity' => 16,
                'location' => 'Floor 3, Room 302',
            ],
        ];

        foreach ($roomsData as $data) {
            $room = new ConferenceRoom();
            $room->setName($data['name']);
            $room->setDescription($data['description']);
            $room->setCapacity($data['capacity']);
            $room->setLocation($data['location']);

            $manager->persist($room);
            $this->addReference($data['reference'], $room);
        }

        $manager->flush();
    }
}
