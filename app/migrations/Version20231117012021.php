<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231117012021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE smfn_achievement DROP CONSTRAINT fk_97346240a76ed395');
        $this->addSql('DROP INDEX idx_97346240a76ed395');
        $this->addSql('ALTER TABLE smfn_achievement RENAME COLUMN user_id TO owner_id');
        $this->addSql('ALTER TABLE smfn_achievement ADD CONSTRAINT FK_973462407E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_973462407E3C61F9 ON smfn_achievement (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_achievement DROP CONSTRAINT FK_973462407E3C61F9');
        $this->addSql('DROP INDEX IDX_973462407E3C61F9');
        $this->addSql('ALTER TABLE smfn_achievement RENAME COLUMN owner_id TO user_id');
        $this->addSql('ALTER TABLE smfn_achievement ADD CONSTRAINT fk_97346240a76ed395 FOREIGN KEY (user_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_97346240a76ed395 ON smfn_achievement (user_id)');
    }
}
