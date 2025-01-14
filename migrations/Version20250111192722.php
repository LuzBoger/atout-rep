<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250111192722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id SERIAL NOT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(10) NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE dates (id SERIAL NOT NULL, request_id INT DEFAULT NULL, address_id INT DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AB74C91E427EB8A5 ON dates (request_id)');
        $this->addSql('CREATE INDEX IDX_AB74C91EF5B7AF75 ON dates (address_id)');
        $this->addSql('CREATE TABLE photos (id SERIAL NOT NULL, object_hs_id INT DEFAULT NULL, home_repair_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, photo_path VARCHAR(255) NOT NULL, upload_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_876E0D9BC98C23 ON photos (object_hs_id)');
        $this->addSql('CREATE INDEX IDX_876E0D93D5745BB ON photos (home_repair_id)');
        $this->addSql('ALTER TABLE dates ADD CONSTRAINT FK_AB74C91E427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dates ADD CONSTRAINT FK_AB74C91EF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D9BC98C23 FOREIGN KEY (object_hs_id) REFERENCES object_hs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D93D5745BB FOREIGN KEY (home_repair_id) REFERENCES home_repair (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer ADD address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E09F5B7AF75 ON customer (address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT FK_81398E09F5B7AF75');
        $this->addSql('ALTER TABLE dates DROP CONSTRAINT FK_AB74C91E427EB8A5');
        $this->addSql('ALTER TABLE dates DROP CONSTRAINT FK_AB74C91EF5B7AF75');
        $this->addSql('ALTER TABLE photos DROP CONSTRAINT FK_876E0D9BC98C23');
        $this->addSql('ALTER TABLE photos DROP CONSTRAINT FK_876E0D93D5745BB');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE dates');
        $this->addSql('DROP TABLE photos');
        $this->addSql('DROP INDEX UNIQ_81398E09F5B7AF75');
        $this->addSql('ALTER TABLE customer DROP address_id');
    }
}
