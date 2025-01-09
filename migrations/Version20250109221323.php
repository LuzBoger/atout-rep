<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109221323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE home_repair (id INT NOT NULL, description VARCHAR(500) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE object_hs (id INT NOT NULL, name VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, age INT NOT NULL, details VARCHAR(500) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE painting (id INT NOT NULL, surface_area INT NOT NULL, paint_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE request (id SERIAL NOT NULL, client_id INT DEFAULT NULL, creation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, modification_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, demande_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3B978F9F19EB6921 ON request (client_id)');
        $this->addSql('CREATE TABLE roofing (id INT NOT NULL, roof_material VARCHAR(255) NOT NULL, need_insulation BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE home_repair ADD CONSTRAINT FK_7EE05D9CBF396750 FOREIGN KEY (id) REFERENCES request (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE object_hs ADD CONSTRAINT FK_B9E5E2ADBF396750 FOREIGN KEY (id) REFERENCES request (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE painting ADD CONSTRAINT FK_66B9EBA0BF396750 FOREIGN KEY (id) REFERENCES request (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F19EB6921 FOREIGN KEY (client_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE roofing ADD CONSTRAINT FK_75B8A9CDBF396750 FOREIGN KEY (id) REFERENCES request (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE home_repair DROP CONSTRAINT FK_7EE05D9CBF396750');
        $this->addSql('ALTER TABLE object_hs DROP CONSTRAINT FK_B9E5E2ADBF396750');
        $this->addSql('ALTER TABLE painting DROP CONSTRAINT FK_66B9EBA0BF396750');
        $this->addSql('ALTER TABLE request DROP CONSTRAINT FK_3B978F9F19EB6921');
        $this->addSql('ALTER TABLE roofing DROP CONSTRAINT FK_75B8A9CDBF396750');
        $this->addSql('DROP TABLE home_repair');
        $this->addSql('DROP TABLE object_hs');
        $this->addSql('DROP TABLE painting');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE roofing');
    }
}
