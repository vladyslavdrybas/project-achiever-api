<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018120610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_achievement (id UUID NOT NULL, user_id UUID NOT NULL, title VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, is_public BOOLEAN DEFAULT false NOT NULL, done_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_97346240A76ED395 ON smfn_achievement (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement.done_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE smfn_achievement_tag (achievement_id UUID NOT NULL, tag_id VARCHAR(30) NOT NULL, PRIMARY KEY(achievement_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_4BE41C1AB3EC99FE ON smfn_achievement_tag (achievement_id)');
        $this->addSql('CREATE INDEX IDX_4BE41C1ABAD26311 ON smfn_achievement_tag (tag_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_tag.achievement_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_tag (id VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE smfn_token (id VARCHAR(200) NOT NULL, user_id UUID NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FA18429EA76ED395 ON smfn_token (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_token.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_token.expire_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE smfn_user (id UUID NOT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(100) NOT NULL, is_email_verified BOOLEAN DEFAULT false NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, is_banned BOOLEAN DEFAULT false NOT NULL, is_deleted BOOLEAN DEFAULT false NOT NULL, locale VARCHAR(5) DEFAULT \'en\' NOT NULL, timezone VARCHAR(30) DEFAULT \'UTC\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8C5186EE7927C74 ON smfn_user (email)');
        $this->addSql('COMMENT ON COLUMN smfn_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_achievement ADD CONSTRAINT FK_97346240A76ED395 FOREIGN KEY (user_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_achievement_tag ADD CONSTRAINT FK_4BE41C1AB3EC99FE FOREIGN KEY (achievement_id) REFERENCES smfn_achievement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_achievement_tag ADD CONSTRAINT FK_4BE41C1ABAD26311 FOREIGN KEY (tag_id) REFERENCES smfn_tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_token ADD CONSTRAINT FK_FA18429EA76ED395 FOREIGN KEY (user_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_achievement DROP CONSTRAINT FK_97346240A76ED395');
        $this->addSql('ALTER TABLE smfn_achievement_tag DROP CONSTRAINT FK_4BE41C1AB3EC99FE');
        $this->addSql('ALTER TABLE smfn_achievement_tag DROP CONSTRAINT FK_4BE41C1ABAD26311');
        $this->addSql('ALTER TABLE smfn_token DROP CONSTRAINT FK_FA18429EA76ED395');
        $this->addSql('DROP TABLE smfn_achievement');
        $this->addSql('DROP TABLE smfn_achievement_tag');
        $this->addSql('DROP TABLE smfn_tag');
        $this->addSql('DROP TABLE smfn_token');
        $this->addSql('DROP TABLE smfn_user');
    }
}
