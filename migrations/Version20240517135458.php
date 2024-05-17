<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517135458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE month_activity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE month_activity (id INT NOT NULL, driver_id_id INT NOT NULL, month VARCHAR(255) NOT NULL, total_points INT NOT NULL, total_distance INT NOT NULL, average_distance INT NOT NULL, total_work_days INT NOT NULL, total_drive INT NOT NULL, total_work INT NOT NULL, days JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EA0A0BA9FFC6537A ON month_activity (driver_id_id)');
        $this->addSql('ALTER TABLE month_activity ADD CONSTRAINT FK_EA0A0BA9FFC6537A FOREIGN KEY (driver_id_id) REFERENCES driver (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE month_activity_id_seq CASCADE');
        $this->addSql('ALTER TABLE month_activity DROP CONSTRAINT FK_EA0A0BA9FFC6537A');
        $this->addSql('DROP TABLE month_activity');
    }
}
