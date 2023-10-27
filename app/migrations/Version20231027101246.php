<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231027101246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_48e4cbe8f85e0677');
        $this->addSql('ALTER TABLE smfn_refresh_tokens ALTER username TYPE VARCHAR(128)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_refresh_tokens ALTER username TYPE VARCHAR(255)');
        $this->addSql('CREATE UNIQUE INDEX uniq_48e4cbe8f85e0677 ON smfn_refresh_tokens (username)');
    }
}
