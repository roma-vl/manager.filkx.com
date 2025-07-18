<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project;

use App\ReadModel\Work\Projects\Project\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use UnexpectedValueException;

final class ProjectFetcher
{
    public function __construct(
        private readonly Connection $connection,
        private readonly PaginatorInterface $paginator,
    ) {}

    /**
     * @throws Exception
     */
    public function getMaxSort(): int
    {
        $result = $this->connection->createQueryBuilder()
            ->select('MAX(p.sort) AS m')
            ->from('work_projects_projects', 'p')
            ->executeQuery()
            ->fetchAssociative();

        return isset($result['m']) ? (int) $result['m'] : 0;
    }

    /**
     * @return array<string, string>
     * @throws Exception
     */
    public function allList(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from('work_projects_projects')
            ->orderBy('sort')
            ->executeQuery()
            ->fetchAllKeyValue();
    }

    /**
     * @throws Exception
     */
    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('p.id', 'p.name', 'p.status')
            ->from('work_projects_projects', 'p');

        if ($filter->member) {
            $qb
                ->andWhere('EXISTS (
                    SELECT 1 FROM work_projects_project_memberships ms
                    WHERE ms.project_id = p.id AND ms.member_id = :member
                )')
                ->setParameter('member', $filter->member);
        }

        if ($filter->name) {
            $qb
                ->andWhere('LOWER(p.name) LIKE :name')
                ->setParameter('name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->status) {
            $qb
                ->andWhere('p.status = :status')
                ->setParameter('status', $filter->status);
        }

        if (!in_array($sort, ['sort', 'name', 'status'], true)) {
            throw new UnexpectedValueException(sprintf('Cannot sort by "%s"', $sort));
        }

        $qb->orderBy('p.' . $sort, $direction === 'desc' ? 'DESC' : 'ASC');

        return $this->paginator->paginate($qb, $page, $size);
    }
}
