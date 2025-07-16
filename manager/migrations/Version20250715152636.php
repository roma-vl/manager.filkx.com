<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715152636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE work_members_groups (id UUID NOT NULL, name VARCHAR(255) NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN work_members_groups.id IS \'(DC2Type:work_members_group_id)\'');
        $this->addSql('ALTER TABLE user_users DROP version');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE work_members_groups');
        $this->addSql('ALTER TABLE user_users ADD version INT DEFAULT 1 NOT NULL');
    }
}
