<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808123735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE accounts (id UUID NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(10) DEFAULT \'en\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CAC89EAC5E237E06 ON accounts (name)');
        $this->addSql('COMMENT ON COLUMN accounts.created_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql("INSERT INTO accounts (id, name, created_at) VALUES (gen_random_uuid(), 'Default Account', NOW())");

        $this->addSql('ALTER TABLE user_users ADD account_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE user_users ADD locale VARCHAR(10) DEFAULT \'en\' NOT NULL');

        $this->addSql('UPDATE user_users SET account_id = (SELECT id FROM accounts LIMIT 1) WHERE account_id IS NULL');

        $this->addSql('ALTER TABLE user_users ALTER COLUMN account_id SET NOT NULL');
        $this->addSql('ALTER TABLE user_users ADD CONSTRAINT FK_F6415EB19B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F6415EB19B6B5FBA ON user_users (account_id)');

        $this->addSql('ALTER TABLE work_members_groups ADD account_id UUID DEFAULT NULL');
        $this->addSql('UPDATE work_members_groups SET account_id = (SELECT id FROM accounts LIMIT 1) WHERE account_id IS NULL');
        $this->addSql('ALTER TABLE work_members_groups ALTER COLUMN account_id SET NOT NULL');
        $this->addSql('ALTER TABLE work_members_groups ADD CONSTRAINT FK_398737B29B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_398737B29B6B5FBA ON work_members_groups (account_id)');

        $this->addSql('ALTER TABLE work_members_members ADD account_id UUID DEFAULT NULL');
        $this->addSql('UPDATE work_members_members SET account_id = (SELECT id FROM accounts LIMIT 1) WHERE account_id IS NULL');
        $this->addSql('ALTER TABLE work_members_members ALTER COLUMN account_id SET NOT NULL');
        $this->addSql('ALTER TABLE work_members_members ADD CONSTRAINT FK_30039B6D9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_30039B6D9B6B5FBA ON work_members_members (account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_users DROP CONSTRAINT FK_F6415EB19B6B5FBA');
        $this->addSql('ALTER TABLE work_members_groups DROP CONSTRAINT FK_398737B29B6B5FBA');
        $this->addSql('ALTER TABLE work_members_members DROP CONSTRAINT FK_30039B6D9B6B5FBA');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP INDEX IDX_30039B6D9B6B5FBA');
        $this->addSql('ALTER TABLE work_members_members DROP account_id');
        $this->addSql('DROP INDEX IDX_398737B29B6B5FBA');
        $this->addSql('ALTER TABLE work_members_groups DROP account_id');
        $this->addSql('DROP INDEX IDX_F6415EB19B6B5FBA');
        $this->addSql('ALTER TABLE user_users DROP account_id');
        $this->addSql('ALTER TABLE user_users DROP language');
    }
}
