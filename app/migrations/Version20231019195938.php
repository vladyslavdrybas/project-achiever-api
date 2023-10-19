<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231019195938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE smfn_token DROP CONSTRAINT fk_fa18429ea76ed395');
        $this->addSql('DROP TABLE smfn_token');
        $this->addSql('ALTER TABLE smfn_user DROP timezone');
        $this->addSql('ALTER TABLE smfn_user DROP watch_public_achievements');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE smfn_token (id VARCHAR(200) NOT NULL, user_id UUID NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_fa18429ea76ed395 ON smfn_token (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_token.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_token.expire_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE smfn_token ADD CONSTRAINT fk_fa18429ea76ed395 FOREIGN KEY (user_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_user ADD timezone VARCHAR(30) DEFAULT \'UTC\' NOT NULL');
        $this->addSql('ALTER TABLE smfn_user ADD watch_public_achievements BOOLEAN DEFAULT false NOT NULL');
    }
}
