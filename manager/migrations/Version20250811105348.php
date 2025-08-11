<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250811105348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add account_id to work_projects_* tables with existing data migration';
    }

    public function up(Schema $schema): void
    {
        // work_projects_project_departments
        $this->addSql('ALTER TABLE work_projects_project_departments ADD account_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN work_projects_project_departments.account_id IS \'(DC2Type:user_account_id)\'');
        $this->addSql('UPDATE work_projects_project_departments SET account_id = (SELECT id FROM accounts LIMIT 1) WHERE account_id IS NULL');
        $this->addSql('ALTER TABLE work_projects_project_departments ALTER COLUMN account_id SET NOT NULL');
        $this->addSql('ALTER TABLE work_projects_project_departments ADD CONSTRAINT FK_F870303A9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F870303A9B6B5FBA ON work_projects_project_departments (account_id)');

        // work_projects_projects
        $this->addSql('ALTER TABLE work_projects_projects ADD account_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN work_projects_projects.account_id IS \'(DC2Type:user_account_id)\'');
        $this->addSql('UPDATE work_projects_projects SET account_id = (SELECT id FROM accounts LIMIT 1) WHERE account_id IS NULL');
        $this->addSql('ALTER TABLE work_projects_projects ALTER COLUMN account_id SET NOT NULL');
        $this->addSql('ALTER TABLE work_projects_projects ADD CONSTRAINT FK_502D96A69B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_502D96A69B6B5FBA ON work_projects_projects (account_id)');

        // work_projects_roles
        $this->addSql('ALTER TABLE work_projects_roles ADD account_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN work_projects_roles.account_id IS \'(DC2Type:user_account_id)\'');
        $this->addSql('UPDATE work_projects_roles SET account_id = (SELECT id FROM accounts LIMIT 1) WHERE account_id IS NULL');
        $this->addSql('ALTER TABLE work_projects_roles ALTER COLUMN account_id SET NOT NULL');
        $this->addSql('ALTER TABLE work_projects_roles ADD CONSTRAINT FK_24B53359B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_24B53359B6B5FBA ON work_projects_roles (account_id)');

        // work_projects_task_changes
        $this->addSql('ALTER TABLE work_projects_task_changes ADD account_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN work_projects_task_changes.account_id IS \'(DC2Type:user_account_id)\'');
        $this->addSql('UPDATE work_projects_task_changes SET account_id = (SELECT id FROM accounts LIMIT 1) WHERE account_id IS NULL');
        $this->addSql('ALTER TABLE work_projects_task_changes ALTER COLUMN account_id SET NOT NULL');
        $this->addSql('ALTER TABLE work_projects_task_changes ADD CONSTRAINT FK_D8BE114A9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D8BE114A9B6B5FBA ON work_projects_task_changes (account_id)');

        // work_projects_tasks
        $this->addSql('ALTER TABLE work_projects_tasks ADD account_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN work_projects_tasks.account_id IS \'(DC2Type:user_account_id)\'');
        $this->addSql('UPDATE work_projects_tasks SET account_id = (SELECT id FROM accounts LIMIT 1) WHERE account_id IS NULL');
        $this->addSql('ALTER TABLE work_projects_tasks ALTER COLUMN account_id SET NOT NULL');
        $this->addSql('ALTER TABLE work_projects_tasks ADD CONSTRAINT FK_E42D18659B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E42D18659B6B5FBA ON work_projects_tasks (account_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE work_projects_tasks DROP CONSTRAINT FK_E42D18659B6B5FBA');
        $this->addSql('DROP INDEX IDX_E42D18659B6B5FBA');
        $this->addSql('ALTER TABLE work_projects_tasks DROP account_id');

        $this->addSql('ALTER TABLE work_projects_task_changes DROP CONSTRAINT FK_D8BE114A9B6B5FBA');
        $this->addSql('DROP INDEX IDX_D8BE114A9B6B5FBA');
        $this->addSql('ALTER TABLE work_projects_task_changes DROP account_id');

        $this->addSql('ALTER TABLE work_projects_roles DROP CONSTRAINT FK_24B53359B6B5FBA');
        $this->addSql('DROP INDEX IDX_24B53359B6B5FBA');
        $this->addSql('ALTER TABLE work_projects_roles DROP account_id');

        $this->addSql('ALTER TABLE work_projects_project_departments DROP CONSTRAINT FK_F870303A9B6B5FBA');
        $this->addSql('DROP INDEX IDX_F870303A9B6B5FBA');
        $this->addSql('ALTER TABLE work_projects_project_departments DROP account_id');

        $this->addSql('ALTER TABLE work_projects_projects DROP CONSTRAINT FK_502D96A69B6B5FBA');
        $this->addSql('DROP INDEX IDX_502D96A69B6B5FBA');
        $this->addSql('ALTER TABLE work_projects_projects DROP account_id');
    }
}
