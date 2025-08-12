<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class RoleFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function allList(string $account): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('work_projects_roles')
            ->where('account_id = :account')
            ->setParameter('account', $account)
            ->orderBy('name')
            ->executeQuery();

        return $stmt->fetchAllKeyValue();
    }

    /**
     * @throws Exception
     */
    public function all(string $account): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'r.id',
                'r.name',
                'r.permissions',
                '(SELECT COUNT(*) FROM work_projects_project_membership_roles m WHERE m.role_id = r.id) AS memberships_count'
            )
            ->from('work_projects_roles', 'r')
            ->where('account_id = :account')
            ->setParameter('account', $account)
            ->orderBy('name')
            ->executeQuery();

        return array_map(static function (array $role) {
            return array_replace($role, [
                'permissions' => json_decode($role['permissions'], true),
            ]);
        }, $stmt->fetchAllAssociative());
    }
}
