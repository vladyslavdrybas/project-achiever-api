<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231115201409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_achievement_prerequisite_relation (id UUID NOT NULL, achievement_id UUID NOT NULL, prerequisite_id UUID NOT NULL, condition VARCHAR(255) DEFAULT \'complete\' NOT NULL, priority INT DEFAULT 0 NOT NULL, is_required BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_19CD7EBAB3EC99FE ON smfn_achievement_prerequisite_relation (achievement_id)');
        $this->addSql('CREATE INDEX IDX_19CD7EBA276AF86B ON smfn_achievement_prerequisite_relation (prerequisite_id)');
        $this->addSql('CREATE UNIQUE INDEX achievement_prerequisite_idx ON smfn_achievement_prerequisite_relation (achievement_id, prerequisite_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_prerequisite_relation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_prerequisite_relation.achievement_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_prerequisite_relation.prerequisite_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_achievement_prerequisite_relation ADD CONSTRAINT FK_19CD7EBAB3EC99FE FOREIGN KEY (achievement_id) REFERENCES smfn_achievement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_achievement_prerequisite_relation ADD CONSTRAINT FK_19CD7EBA276AF86B FOREIGN KEY (prerequisite_id) REFERENCES smfn_achievement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_achievement_prerequisite_relation DROP CONSTRAINT FK_19CD7EBAB3EC99FE');
        $this->addSql('ALTER TABLE smfn_achievement_prerequisite_relation DROP CONSTRAINT FK_19CD7EBA276AF86B');
        $this->addSql('DROP TABLE smfn_achievement_prerequisite_relation');
    }
}
