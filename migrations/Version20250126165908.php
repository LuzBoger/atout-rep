<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250126165908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE customer_id_seq CASCADE');
        $this->addSql('CREATE TABLE accounts (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, reset_password_token VARCHAR(255) DEFAULT NULL, reset_token_expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, role_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN accounts.reset_token_expire_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, account_creation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE provider (id INT NOT NULL, price_fdp NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE provider ADD CONSTRAINT FK_92C4739CBF396750 FOREIGN KEY (id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer DROP name');
        $this->addSql('ALTER TABLE customer DROP surname');
        $this->addSql('ALTER TABLE customer DROP email');
        $this->addSql('ALTER TABLE customer DROP phone');
        $this->addSql('ALTER TABLE customer DROP password');
        $this->addSql('ALTER TABLE customer DROP roles');
        $this->addSql('ALTER TABLE customer DROP reset_password_token');
        $this->addSql('ALTER TABLE customer DROP reset_token_expire_at');
        $this->addSql('ALTER TABLE customer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09BF396750 FOREIGN KEY (id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT FK_81398E09BF396750');
        $this->addSql('CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE admin DROP CONSTRAINT FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE provider DROP CONSTRAINT FK_92C4739CBF396750');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE provider');
        $this->addSql('ALTER TABLE customer ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD surname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD phone VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE customer ADD reset_password_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD reset_token_expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE SEQUENCE customer_id_seq');
        $this->addSql('SELECT setval(\'customer_id_seq\', (SELECT MAX(id) FROM customer))');
        $this->addSql('ALTER TABLE customer ALTER id SET DEFAULT nextval(\'customer_id_seq\')');
        $this->addSql('COMMENT ON COLUMN customer.reset_token_expire_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
