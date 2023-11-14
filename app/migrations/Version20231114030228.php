<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231114030228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_share_object_token (id VARCHAR(128) NOT NULL, owner_id UUID NOT NULL, object VARCHAR(144) NOT NULL, object_id VARCHAR(36) NOT NULL, can_view BOOLEAN DEFAULT true NOT NULL, can_edit BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5936F077E3C61F9 ON smfn_share_object_token (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_share_object_token.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_share_object_token ADD CONSTRAINT FK_5936F077E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_share_object_token DROP CONSTRAINT FK_5936F077E3C61F9');
        $this->addSql('DROP TABLE smfn_share_object_token');
    }
}
