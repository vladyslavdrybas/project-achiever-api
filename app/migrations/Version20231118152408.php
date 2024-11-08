<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231118152408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_tag (id VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_analytics_track_notification (id UUID NOT NULL, user_id UUID NOT NULL, message JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9b4b12fa76ed395 ON smfn_analytics_track_notification (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_analytics_track_notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_analytics_track_notification.user_id IS \'(DC2Type:uuid)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_achievement_prerequisite_relation (id UUID NOT NULL, achievement_id UUID NOT NULL, prerequisite_id UUID NOT NULL, condition VARCHAR(255) DEFAULT \'complete\' NOT NULL, priority INT DEFAULT 0 NOT NULL, is_required BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX achievement_prerequisite_idx ON smfn_achievement_prerequisite_relation (achievement_id, prerequisite_id)');
        $this->addSql('CREATE INDEX idx_19cd7eba276af86b ON smfn_achievement_prerequisite_relation (prerequisite_id)');
        $this->addSql('CREATE INDEX idx_19cd7ebab3ec99fe ON smfn_achievement_prerequisite_relation (achievement_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_prerequisite_relation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_prerequisite_relation.achievement_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_prerequisite_relation.prerequisite_id IS \'(DC2Type:uuid)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_achievement_list (id UUID NOT NULL, owner_id UUID NOT NULL, title VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_public BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX owner_title_idx ON smfn_achievement_list (owner_id, title)');
        $this->addSql('CREATE INDEX idx_cd53be537e3c61f9 ON smfn_achievement_list (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list.owner_id IS \'(DC2Type:uuid)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_user (id UUID NOT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(100) NOT NULL, is_email_verified BOOLEAN DEFAULT false NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, is_banned BOOLEAN DEFAULT false NOT NULL, is_deleted BOOLEAN DEFAULT false NOT NULL, locale VARCHAR(5) DEFAULT \'en\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, username VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_a8c5186ef85e0677 ON smfn_user (username)');
        $this->addSql('CREATE UNIQUE INDEX uniq_a8c5186ee7927c74 ON smfn_user (email)');
        $this->addSql('COMMENT ON COLUMN smfn_user.id IS \'(DC2Type:uuid)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_firebase_cloud_messaging (id UUID NOT NULL, user_id UUID NOT NULL, token VARCHAR(512) NOT NULL, device_type VARCHAR(10) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_ff51f873a76ed395 ON smfn_firebase_cloud_messaging (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_firebase_cloud_messaging.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_firebase_cloud_messaging.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_firebase_cloud_messaging.expire_at IS \'(DC2Type:datetime_immutable)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_achievement (id UUID NOT NULL, owner_id UUID NOT NULL, title VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, is_public BOOLEAN DEFAULT false NOT NULL, done_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, notified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_notified BOOLEAN DEFAULT false NOT NULL, content_image_link VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_973462407e3c61f9 ON smfn_achievement (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement.done_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement.notified_at IS \'(DC2Type:datetime_immutable)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_achievement_list_group_relation (list_id UUID NOT NULL, user_group_id UUID NOT NULL, PRIMARY KEY(list_id, user_group_id))');
        $this->addSql('CREATE INDEX idx_b151a4651ed93d47 ON smfn_achievement_list_group_relation (user_group_id)');
        $this->addSql('CREATE INDEX idx_b151a4653dae168b ON smfn_achievement_list_group_relation (list_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list_group_relation.list_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list_group_relation.user_group_id IS \'(DC2Type:uuid)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_share_object_token (id VARCHAR(64) NOT NULL, owner_id UUID NOT NULL, target VARCHAR(144) NOT NULL, target_id VARCHAR(36) NOT NULL, can_edit BOOLEAN DEFAULT false NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, hash VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_5936f07d1b862b8 ON smfn_share_object_token (hash)');
        $this->addSql('CREATE INDEX idx_5936f077e3c61f9 ON smfn_share_object_token (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_share_object_token.owner_id IS \'(DC2Type:uuid)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_achievement_list_relation (list_id UUID NOT NULL, achievement_id UUID NOT NULL, PRIMARY KEY(list_id, achievement_id))');
        $this->addSql('CREATE INDEX idx_7ba1454fb3ec99fe ON smfn_achievement_list_relation (achievement_id)');
        $this->addSql('CREATE INDEX idx_7ba1454f3dae168b ON smfn_achievement_list_relation (list_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list_relation.list_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_list_relation.achievement_id IS \'(DC2Type:uuid)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_user_group_relation (id UUID NOT NULL, member_id UUID NOT NULL, user_group_id UUID NOT NULL, title VARCHAR(10) NOT NULL, can_view BOOLEAN DEFAULT true NOT NULL, can_edit BOOLEAN DEFAULT false NOT NULL, can_delete BOOLEAN DEFAULT false NOT NULL, can_manage BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX member_group_idx ON smfn_user_group_relation (member_id, user_group_id)');
        $this->addSql('CREATE INDEX idx_3354ec3c7597d3fe ON smfn_user_group_relation (member_id)');
        $this->addSql('CREATE INDEX idx_3354ec3c1ed93d47 ON smfn_user_group_relation (user_group_id)');
        $this->addSql('COMMENT ON COLUMN smfn_user_group_relation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_group_relation.member_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_group_relation.user_group_id IS \'(DC2Type:uuid)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_user_group (id UUID NOT NULL, owner_id UUID NOT NULL, title VARCHAR(125) NOT NULL, description VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX owner_group_title_idx ON smfn_user_group (owner_id, title)');
        $this->addSql('CREATE INDEX idx_136e08dc7e3c61f9 ON smfn_user_group (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_user_group.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_group.owner_id IS \'(DC2Type:uuid)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_achievement_tag (achievement_id UUID NOT NULL, tag_id VARCHAR(30) NOT NULL, PRIMARY KEY(achievement_id, tag_id))');
        $this->addSql('CREATE INDEX idx_4be41c1abad26311 ON smfn_achievement_tag (tag_id)');
        $this->addSql('CREATE INDEX idx_4be41c1ab3ec99fe ON smfn_achievement_tag (achievement_id)');
        $this->addSql('COMMENT ON COLUMN smfn_achievement_tag.achievement_id IS \'(DC2Type:uuid)\'');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE smfn_refresh_tokens (id UUID NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(36) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_48e4cbe8c74f2195 ON smfn_refresh_tokens (refresh_token)');
        $this->addSql('COMMENT ON COLUMN smfn_refresh_tokens.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_tag');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_analytics_track_notification');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_achievement_prerequisite_relation');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_achievement_list');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_user');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_firebase_cloud_messaging');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_achievement');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_achievement_list_group_relation');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_share_object_token');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_achievement_list_relation');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_user_group_relation');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_user_group');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_achievement_tag');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE smfn_refresh_tokens');
    }
}
