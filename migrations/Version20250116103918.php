<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116103918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE photos_id_seq CASCADE');
        $this->addSql('CREATE TABLE photo (id SERIAL NOT NULL, object_hs_id INT DEFAULT NULL, home_repair_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, photo_path VARCHAR(255) NOT NULL, upload_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14B78418BC98C23 ON photo (object_hs_id)');
        $this->addSql('CREATE INDEX IDX_14B784183D5745BB ON photo (home_repair_id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BC98C23 FOREIGN KEY (object_hs_id) REFERENCES object_hs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784183D5745BB FOREIGN KEY (home_repair_id) REFERENCES home_repair (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photos DROP CONSTRAINT fk_876e0d9bc98c23');
        $this->addSql('ALTER TABLE photos DROP CONSTRAINT fk_876e0d93d5745bb');
        $this->addSql('DROP TABLE photos');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE photos_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE photos (id SERIAL NOT NULL, object_hs_id INT DEFAULT NULL, home_repair_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, photo_path VARCHAR(255) NOT NULL, upload_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_876e0d93d5745bb ON photos (home_repair_id)');
        $this->addSql('CREATE INDEX idx_876e0d9bc98c23 ON photos (object_hs_id)');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT fk_876e0d9bc98c23 FOREIGN KEY (object_hs_id) REFERENCES object_hs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT fk_876e0d93d5745bb FOREIGN KEY (home_repair_id) REFERENCES home_repair (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo DROP CONSTRAINT FK_14B78418BC98C23');
        $this->addSql('ALTER TABLE photo DROP CONSTRAINT FK_14B784183D5745BB');
        $this->addSql('DROP TABLE photo');
    }
}
