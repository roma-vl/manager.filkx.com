<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Members\Member;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Status;
use App\Model\Work\Entity\Projects\Project\Membership;
use App\ReadModel\Work\Members\Member\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class MemberFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;
    private EntityRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(
        Connection $connection,
        PaginatorInterface $paginator,
        EntityManagerInterface $em,
    ) {
        $this->connection = $connection;
        $this->em = $em;
        $this->repository = $em->getRepository(Member::class);
        $this->paginator = $paginator;
    }

    public function find(string $id): ?Member
    {
        return $this->repository->find($id);
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $membershipClass = Membership::class;

        $qb = $this->em->createQueryBuilder()
            ->select('m.id')
            ->addSelect("CONCAT(m.name.first, ' ', m.name.last) AS name")
            ->addSelect('m.email AS email')
            ->addSelect('g.name AS group')
            ->addSelect('m.status AS status')
            ->addSelect("(SELECT COUNT(pm.id) FROM {$membershipClass} pm WHERE pm.member = m) AS memberships_count")
            ->from(Member::class, 'm')
            ->join('m.group', 'g');

        if ($filter->name) {
            $qb->andWhere($qb->expr()->like(
                "LOWER(CONCAT(m.name.first, ' ', m.name.last))",
                ':name'
            ));
            $qb->setParameter('name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->email) {
            $qb->andWhere($qb->expr()->like('LOWER(m.email)', ':email'));
            $qb->setParameter('email', '%' . mb_strtolower($filter->email) . '%');
        }

        if ($filter->status) {
            $qb->andWhere('m.status = :status');
            $qb->setParameter('status', $filter->status);
        }

        if ($filter->group) {
            $qb->andWhere('g.id = :group');
            $qb->setParameter('group', $filter->group);
        }

        $sortMap = [
            'name' => 'name',
            'email' => 'email',
            'group' => 'group_name',
            'status' => 'status',
        ];

        if (!isset($sortMap[$sort])) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sortMap[$sort], $direction === 'desc' ? 'DESC' : 'ASC');

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function exists(string $id): bool
    {
        return $this->connection->createQueryBuilder()
                ->select('COUNT(id)')
                ->from('work_members_members')
                ->where('id = :id')
                ->setParameter('id', $id)
                ->executeQuery()
                ->fetchOne() > 0;
    }

    /**
     * @throws Exception
     */
    public function activeGroupedList(string $account): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select([
                'm.id',
                'CONCAT(m.name_first, \' \', m.name_last) AS name',
                'g.name AS group',
            ])
            ->from('work_members_members', 'm')
            ->leftJoin('m', 'work_members_groups', 'g', 'g.id = m.group_id')
            ->andWhere('m.account_id = :account') // ← додав префікс
            ->setParameter('account', $account)
            ->andWhere('m.status = :status')
            ->setParameter('status', Status::ACTIVE)
            ->orderBy('g.name')->addOrderBy('name')
            ->executeQuery();

        return $stmt->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function activeDepartmentListForProject(string $project): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select([
                'm.id',
                'CONCAT(m.name_first, \' \', m.name_last) AS name',
                'd.name AS department',
            ])
            ->from('work_members_members', 'm')
            ->innerJoin('m', 'work_projects_project_memberships', 'ms', 'ms.member_id = m.id')
            ->innerJoin('ms', 'work_projects_project_membership_departments', 'msd', 'msd.membership_id = ms.id')
            ->innerJoin('msd', 'work_projects_project_departments', 'd', 'd.id = msd.department_id')
            ->andWhere('m.status = :status AND ms.project_id = :project')
            ->setParameter('status', Status::ACTIVE)
            ->setParameter('project', $project)
            ->orderBy('d.name')->addOrderBy('name')
            ->executeQuery();

        return $stmt->fetchAllAssociative();
    }
}
