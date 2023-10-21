<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231021123932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_token (id VARCHAR(128) NOT NULL, user_id UUID NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FA18429EA76ED395 ON smfn_token (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_token.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_token.expire_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE smfn_token ADD CONSTRAINT FK_FA18429EA76ED395 FOREIGN KEY (user_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_token DROP CONSTRAINT FK_FA18429EA76ED395');
        $this->addSql('DROP TABLE smfn_token');
    }
}
