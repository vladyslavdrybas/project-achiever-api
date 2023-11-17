<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231117012337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE smfn_user_group_relation DROP CONSTRAINT fk_3354ec3ca76ed395');
        $this->addSql('DROP INDEX idx_3354ec3ca76ed395');
        $this->addSql('DROP INDEX member_group_idx');
        $this->addSql('ALTER TABLE smfn_user_group_relation RENAME COLUMN user_id TO member_id');
        $this->addSql('ALTER TABLE smfn_user_group_relation ADD CONSTRAINT FK_3354EC3C7597D3FE FOREIGN KEY (member_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3354EC3C7597D3FE ON smfn_user_group_relation (member_id)');
        $this->addSql('CREATE UNIQUE INDEX member_group_idx ON smfn_user_group_relation (member_id, user_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_user_group_relation DROP CONSTRAINT FK_3354EC3C7597D3FE');
        $this->addSql('DROP INDEX IDX_3354EC3C7597D3FE');
        $this->addSql('DROP INDEX member_group_idx');
        $this->addSql('ALTER TABLE smfn_user_group_relation RENAME COLUMN member_id TO user_id');
        $this->addSql('ALTER TABLE smfn_user_group_relation ADD CONSTRAINT fk_3354ec3ca76ed395 FOREIGN KEY (user_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3354ec3ca76ed395 ON smfn_user_group_relation (user_id)');
        $this->addSql('CREATE UNIQUE INDEX member_group_idx ON smfn_user_group_relation (user_id, user_group_id)');
    }
}
