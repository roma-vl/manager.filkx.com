<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Calendar;

use App\ReadModel\Work\Projects\Calendar\Query\Query;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;

class CalendarFetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function byMonth(Query $query): Result
    {
        $month = new \DateTimeImmutable(\sprintf('%d-%02d-01', $query->year, $query->month));
        $start = self::calcFirstDayOfWeek($month)->setTime(0, 0);
        $end = $start->modify('+34 days')->setTime(23, 59, 59);

        $qb = $this->buildQuery($start, $end);

        if ($query->member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 't.project_id = ms.project_id');
            $qb->andWhere('ms.member_id = :member');
            $qb->setParameter('member', $query->member);
        }

        if ($query->project) {
            $qb->andWhere('t.project_id = :project');
            $qb->setParameter('project', $query->project);
        }

        $stmt = $qb->executeQuery();

        return new Result($stmt->fetchAllAssociative(), $start, $end, $month);
    }

    /**
     * @throws \Exception
     */
    public function byWeek(\DateTimeImmutable $date, ?string $member): Result
    {
        $start = self::calcFirstDayOfWeek($date)->setTime(0, 0);
        $end = $start->modify('+6 days')->setTime(23, 59, 59);

        $qb = $this->buildQuery($start, $end);

        if ($member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 't.project_id = ms.project_id');
            $qb->andWhere('ms.member_id = :member');
            $qb->setParameter('member', $member);
        }

        $stmt = $qb->executeQuery();

        return new Result($stmt->fetchAllAssociative(), $start, $end, $date);
    }

    private function buildQuery(\DateTimeImmutable $start, \DateTimeImmutable $end): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb
            ->select(
                't.id',
                't.name',
                'p.id AS project_id',
                'p.name AS project_name',
                "to_char(t.date, 'YYYY-MM-DD') AS date",
                't.plan_date',
                't.start_date',
                't.end_date'
            )
            ->from('work_projects_tasks', 't')
            ->leftJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->andWhere($qb->expr()->orX(
                't.date BETWEEN :start AND :end',
                't.plan_date BETWEEN :start AND :end',
                't.start_date BETWEEN :start AND :end',
                't.end_date BETWEEN :start AND :end'
            ))
            ->setParameter('start', $start, Types::DATETIME_IMMUTABLE)
            ->setParameter('end', $end, Types::DATETIME_IMMUTABLE)
            ->orderBy('date');
    }

    private static function calcFirstDayOfWeek(\DateTimeImmutable $date): \DateTimeImmutable
    {
        $weekday = (int) $date->format('w');

        return $weekday === 0 ? $date->modify('-6 days') : $date->modify('-' . ($weekday - 1) . ' days');
    }
}
