<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231112064011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_user_group (id UUID NOT NULL, owner_id UUID NOT NULL, title VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_136E08DC7E3C61F9 ON smfn_user_group (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX owner_group_title_idx ON smfn_user_group (owner_id, title)');
        $this->addSql('COMMENT ON COLUMN smfn_user_group.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_group.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_user_group_relation (id UUID NOT NULL, user_id UUID NOT NULL, user_group_id UUID NOT NULL, title VARCHAR(10) NOT NULL, can_view BOOLEAN DEFAULT true NOT NULL, can_edit BOOLEAN DEFAULT false NOT NULL, can_delete BOOLEAN DEFAULT false NOT NULL, can_manage_members BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3354EC3CA76ED395 ON smfn_user_group_relation (user_id)');
        $this->addSql('CREATE INDEX IDX_3354EC3C1ED93D47 ON smfn_user_group_relation (user_group_id)');
        $this->addSql('CREATE UNIQUE INDEX member_group_idx ON smfn_user_group_relation (user_id, user_group_id)');
        $this->addSql('COMMENT ON COLUMN smfn_user_group_relation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_group_relation.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_group_relation.user_group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_user_group ADD CONSTRAINT FK_136E08DC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_user_group_relation ADD CONSTRAINT FK_3354EC3CA76ED395 FOREIGN KEY (user_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_user_group_relation ADD CONSTRAINT FK_3354EC3C1ED93D47 FOREIGN KEY (user_group_id) REFERENCES smfn_user_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_user_group DROP CONSTRAINT FK_136E08DC7E3C61F9');
        $this->addSql('ALTER TABLE smfn_user_group_relation DROP CONSTRAINT FK_3354EC3CA76ED395');
        $this->addSql('ALTER TABLE smfn_user_group_relation DROP CONSTRAINT FK_3354EC3C1ED93D47');
        $this->addSql('DROP TABLE smfn_user_group');
        $this->addSql('DROP TABLE smfn_user_group_relation');
    }
}
