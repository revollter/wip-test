<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial migration: Create conference_rooms and reservations tables.
 */
final class Version20240101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create conference_rooms and reservations tables';
    }

    public function up(Schema $schema): void
    {
        // Create conference_rooms table
        $this->addSql('CREATE TABLE conference_rooms (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description VARCHAR(500) DEFAULT NULL,
            capacity INTEGER NOT NULL,
            location VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        )');

        // Create reservations table
        $this->addSql('CREATE TABLE reservations (
            id SERIAL PRIMARY KEY,
            conference_room_id INTEGER NOT NULL,
            reserver_name VARCHAR(255) NOT NULL,
            date DATE NOT NULL,
            start_time TIME(0) WITHOUT TIME ZONE NOT NULL,
            end_time TIME(0) WITHOUT TIME ZONE NOT NULL,
            notes VARCHAR(500) DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            CONSTRAINT fk_conference_room FOREIGN KEY (conference_room_id)
                REFERENCES conference_rooms (id) ON DELETE CASCADE
        )');

        // Create indexes for better query performance
        $this->addSql('CREATE INDEX idx_reservation_room ON reservations (conference_room_id)');
        $this->addSql('CREATE INDEX idx_reservation_date ON reservations (date)');
        $this->addSql('CREATE INDEX idx_reservation_room_date ON reservations (conference_room_id, date)');

        // Create messenger_messages table for failed messages
        $this->addSql('CREATE TABLE messenger_messages (
            id BIGSERIAL PRIMARY KEY,
            body TEXT NOT NULL,
            headers TEXT NOT NULL,
            queue_name VARCHAR(190) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        )');
        $this->addSql('CREATE INDEX idx_messenger_queue ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX idx_messenger_available ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX idx_messenger_delivered ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS reservations');
        $this->addSql('DROP TABLE IF EXISTS conference_rooms');
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
    }
}
