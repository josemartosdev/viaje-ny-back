<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260702102449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE activities (
              id INT AUTO_INCREMENT NOT NULL,
              title VARCHAR(255) NOT NULL,
              category VARCHAR(120) NOT NULL,
              start_time TIME DEFAULT NULL,
              end_time TIME DEFAULT NULL,
              status VARCHAR(255) NOT NULL,
              price NUMERIC(10, 2) DEFAULT NULL,
              currency VARCHAR(3) DEFAULT NULL,
              booking_code VARCHAR(120) DEFAULT NULL,
              notes LONGTEXT DEFAULT NULL,
              day_id INT NOT NULL,
              place_id INT DEFAULT NULL,
              INDEX IDX_B5F1AFE59C24126 (day_id),
              INDEX IDX_B5F1AFE5DA6A219 (place_id),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE days (
              id INT AUTO_INCREMENT NOT NULL,
              date DATE NOT NULL,
              title VARCHAR(255) NOT NULL,
              notes LONGTEXT DEFAULT NULL,
              weather_tip VARCHAR(255) DEFAULT NULL,
              district VARCHAR(120) DEFAULT NULL,
              trip_id INT NOT NULL,
              INDEX IDX_EBE4FC66A5BC2E0E (trip_id),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE places (
              id INT AUTO_INCREMENT NOT NULL,
              name VARCHAR(255) NOT NULL,
              type VARCHAR(255) NOT NULL,
              address VARCHAR(255) DEFAULT NULL,
              lat DOUBLE PRECISION DEFAULT NULL,
              lng DOUBLE PRECISION DEFAULT NULL,
              price_level SMALLINT DEFAULT NULL,
              average_price NUMERIC(10, 2) DEFAULT NULL,
              currency VARCHAR(3) DEFAULT NULL,
              website VARCHAR(255) DEFAULT NULL,
              phone VARCHAR(50) DEFAULT NULL,
              notes LONGTEXT DEFAULT NULL,
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tickets (
              id INT AUTO_INCREMENT NOT NULL,
              type VARCHAR(255) NOT NULL,
              title VARCHAR(255) NOT NULL,
              provider VARCHAR(120) DEFAULT NULL,
              code VARCHAR(120) DEFAULT NULL,
              holder VARCHAR(120) DEFAULT NULL,
              seat VARCHAR(40) DEFAULT NULL,
              gate VARCHAR(40) DEFAULT NULL,
              price NUMERIC(10, 2) DEFAULT NULL,
              currency VARCHAR(3) DEFAULT NULL,
              document_url VARCHAR(500) DEFAULT NULL,
              notes LONGTEXT DEFAULT NULL,
              day_id INT NOT NULL,
              activity_id INT DEFAULT NULL,
              INDEX IDX_54469DF49C24126 (day_id),
              INDEX IDX_54469DF481C06096 (activity_id),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trips (
              id INT AUTO_INCREMENT NOT NULL,
              name VARCHAR(255) NOT NULL,
              city VARCHAR(120) NOT NULL,
              start_date DATE NOT NULL,
              end_date DATE NOT NULL,
              currency VARCHAR(3) NOT NULL,
              notes LONGTEXT DEFAULT NULL,
              created_at DATETIME NOT NULL,
              updated_at DATETIME NOT NULL,
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              activities
            ADD
              CONSTRAINT FK_B5F1AFE59C24126 FOREIGN KEY (day_id) REFERENCES days (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              activities
            ADD
              CONSTRAINT FK_B5F1AFE5DA6A219 FOREIGN KEY (place_id) REFERENCES places (id) ON DELETE
            SET
              NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              days
            ADD
              CONSTRAINT FK_EBE4FC66A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trips (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              tickets
            ADD
              CONSTRAINT FK_54469DF49C24126 FOREIGN KEY (day_id) REFERENCES days (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              tickets
            ADD
              CONSTRAINT FK_54469DF481C06096 FOREIGN KEY (activity_id) REFERENCES activities (id) ON DELETE
            SET
              NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE59C24126');
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE5DA6A219');
        $this->addSql('ALTER TABLE days DROP FOREIGN KEY FK_EBE4FC66A5BC2E0E');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF49C24126');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF481C06096');
        $this->addSql('DROP TABLE activities');
        $this->addSql('DROP TABLE days');
        $this->addSql('DROP TABLE places');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE trips');
    }
}
