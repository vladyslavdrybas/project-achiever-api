<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231114225732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE smfn_share_object_token RENAME COLUMN object TO target');
        $this->addSql('ALTER TABLE smfn_share_object_token RENAME COLUMN object_id TO target_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_share_object_token RENAME COLUMN target TO object');
        $this->addSql('ALTER TABLE smfn_share_object_token RENAME COLUMN target_id TO object_id');
    }
}
