<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127181355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, provider_id INT NOT NULL, name VARCHAR(255) NOT NULL, weight NUMERIC(5, 1) NOT NULL, description VARCHAR(255) NOT NULL, length NUMERIC(6, 2) NOT NULL, width NUMERIC(6, 2) NOT NULL, height NUMERIC(6, 2) NOT NULL, stock SMALLINT NOT NULL, price NUMERIC(7, 2) NOT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04ADA53A8AA ON product (provider_id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784184584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_14B784184584665A ON photo (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE photo DROP CONSTRAINT FK_14B784184584665A');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADA53A8AA');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP INDEX IDX_14B784184584665A');
        $this->addSql('ALTER TABLE photo DROP product_id');
    }
}
