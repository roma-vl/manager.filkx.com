<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808145548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accounts ADD owner_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE accounts ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN accounts.owner_id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN accounts.id IS \'(DC2Type:user_account_id)\'');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EAC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CAC89EAC7E3C61F9 ON accounts (owner_id)');
        $this->addSql('ALTER TABLE user_users ALTER account_id TYPE UUID');
        $this->addSql('ALTER TABLE user_users ALTER locale DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_users.account_id IS \'(DC2Type:user_account_id)\'');
        $this->addSql('ALTER TABLE work_members_groups ALTER account_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN work_members_groups.account_id IS \'(DC2Type:user_account_id)\'');
        $this->addSql('ALTER TABLE work_members_members ALTER account_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN work_members_members.account_id IS \'(DC2Type:user_account_id)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE work_members_groups ALTER account_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN work_members_groups.account_id IS NULL');
        $this->addSql('ALTER TABLE user_users ALTER account_id TYPE UUID');
        $this->addSql('ALTER TABLE user_users ALTER locale SET DEFAULT \'en\'');
        $this->addSql('COMMENT ON COLUMN user_users.account_id IS NULL');
        $this->addSql('ALTER TABLE work_members_members ALTER account_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN work_members_members.account_id IS NULL');
        $this->addSql('ALTER TABLE accounts DROP CONSTRAINT FK_CAC89EAC7E3C61F9');
        $this->addSql('DROP INDEX IDX_CAC89EAC7E3C61F9');
        $this->addSql('ALTER TABLE accounts DROP owner_id');
        $this->addSql('ALTER TABLE accounts ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN accounts.id IS NULL');
    }
}
