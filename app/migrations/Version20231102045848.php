<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231102045848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_analytics_track_notification (id UUID NOT NULL, user_id UUID NOT NULL, message JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9B4B12FA76ED395 ON smfn_analytics_track_notification (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_analytics_track_notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_analytics_track_notification.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_analytics_track_notification ADD CONSTRAINT FK_9B4B12FA76ED395 FOREIGN KEY (user_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_analytics_track_notification DROP CONSTRAINT FK_9B4B12FA76ED395');
        $this->addSql('DROP TABLE smfn_analytics_track_notification');
    }
}
