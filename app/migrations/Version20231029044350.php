<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231029044350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE smfn_firebase_cloud_messaging ADD user_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN smfn_firebase_cloud_messaging.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_firebase_cloud_messaging ADD CONSTRAINT FK_FF51F873A76ED395 FOREIGN KEY (user_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FF51F873A76ED395 ON smfn_firebase_cloud_messaging (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_firebase_cloud_messaging DROP CONSTRAINT FK_FF51F873A76ED395');
        $this->addSql('DROP INDEX IDX_FF51F873A76ED395');
        $this->addSql('ALTER TABLE smfn_firebase_cloud_messaging DROP user_id');
    }
}
