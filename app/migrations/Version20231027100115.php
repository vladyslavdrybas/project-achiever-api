<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231027100115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('ALTER TABLE smfn_refresh_tokens ADD id UUID NOT NULL');
        $this->addSql('ALTER TABLE smfn_refresh_tokens ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE smfn_refresh_tokens ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE smfn_refresh_tokens ALTER valid TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE smfn_refresh_tokens ALTER valid DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN smfn_refresh_tokens.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_refresh_tokens.valid IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_48E4CBE8F85E0677 ON smfn_refresh_tokens (username)');
        $this->addSql('ALTER TABLE smfn_refresh_tokens ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP INDEX UNIQ_48E4CBE8F85E0677');
        $this->addSql('ALTER TABLE smfn_refresh_tokens DROP CONSTRAINT smfn_refresh_tokens_pkey');
        $this->addSql('ALTER TABLE smfn_refresh_tokens DROP id');
        $this->addSql('ALTER TABLE smfn_refresh_tokens DROP created_at');
        $this->addSql('ALTER TABLE smfn_refresh_tokens DROP updated_at');
        $this->addSql('ALTER TABLE smfn_refresh_tokens ALTER valid TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE smfn_refresh_tokens ALTER valid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN smfn_refresh_tokens.valid IS NULL');
    }
}
