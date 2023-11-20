<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231120210618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE smfn_user ADD firstname VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE smfn_user ADD lastname VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE smfn_user ALTER username SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_user DROP firstname');
        $this->addSql('ALTER TABLE smfn_user DROP lastname');
        $this->addSql('ALTER TABLE smfn_user ALTER username DROP NOT NULL');
    }
}
