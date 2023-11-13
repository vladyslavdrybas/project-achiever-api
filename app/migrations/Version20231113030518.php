<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231113030518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_achievement_list (id UUID NOT NULL, owner_id UUID NOT NULL, title VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CD53BE537E3C61F9 ON smfn_achievement_list (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX owner_title_idx ON smfn_achievement_list (owner_id, title)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_achievement_list_relation (list_id UUID NOT NULL, achievement_id UUID NOT NULL, PRIMARY KEY(list_id, achievement_id))');
        $this->addSql('CREATE INDEX IDX_7BA1454F3DAE168B ON smfn_achievement_list_relation (list_id)');
        $this->addSql('CREATE INDEX IDX_7BA1454FB3EC99FE ON smfn_achievement_list_relation (achievement_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list_relation.list_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list_relation.achievement_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_achievement_list_group_relation (list_id UUID NOT NULL, user_group_id UUID NOT NULL, PRIMARY KEY(list_id, user_group_id))');
        $this->addSql('CREATE INDEX IDX_B151A4653DAE168B ON smfn_achievement_list_group_relation (list_id)');
        $this->addSql('CREATE INDEX IDX_B151A4651ED93D47 ON smfn_achievement_list_group_relation (user_group_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list_group_relation.list_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list_group_relation.user_group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_achievement_list ADD CONSTRAINT FK_CD53BE537E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_achievement_list_relation ADD CONSTRAINT FK_7BA1454F3DAE168B FOREIGN KEY (list_id) REFERENCES smfn_achievement_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_achievement_list_relation ADD CONSTRAINT FK_7BA1454FB3EC99FE FOREIGN KEY (achievement_id) REFERENCES smfn_achievement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_achievement_list_group_relation ADD CONSTRAINT FK_B151A4653DAE168B FOREIGN KEY (list_id) REFERENCES smfn_achievement_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_achievement_list_group_relation ADD CONSTRAINT FK_B151A4651ED93D47 FOREIGN KEY (user_group_id) REFERENCES smfn_user_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_achievement_list DROP CONSTRAINT FK_CD53BE537E3C61F9');
        $this->addSql('ALTER TABLE smfn_achievement_list_relation DROP CONSTRAINT FK_7BA1454F3DAE168B');
        $this->addSql('ALTER TABLE smfn_achievement_list_relation DROP CONSTRAINT FK_7BA1454FB3EC99FE');
        $this->addSql('ALTER TABLE smfn_achievement_list_group_relation DROP CONSTRAINT FK_B151A4653DAE168B');
        $this->addSql('ALTER TABLE smfn_achievement_list_group_relation DROP CONSTRAINT FK_B151A4651ED93D47');
        $this->addSql('DROP TABLE smfn_achievement_list');
        $this->addSql('DROP TABLE smfn_achievement_list_relation');
        $this->addSql('DROP TABLE smfn_achievement_list_group_relation');
    }
}
