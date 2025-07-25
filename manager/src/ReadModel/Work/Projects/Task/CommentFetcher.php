<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Task;
use App\ReadModel\Comment\CommentRow;
use Doctrine\DBAL\Connection;

class CommentFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws \Exception
     */
    public function allForTask(int $id): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('c.id, c.text, c.date, m.name_first || \' \' || m.name_last AS author_name, m.email')
            ->from('comment_comments', 'c')
            ->innerJoin('c', 'work_members_members', 'm', 'c.author_id = m.id')
            ->andWhere('c.entity_type = :entity_type AND c.entity_id = :entity_id')
            ->setParameter('entity_type', Task::class)
            ->setParameter('entity_id', $id)
            ->orderBy('c.date')
            ->executeQuery();

        $rows = $result->fetchAllAssociative();

        return array_map(static fn (array $row) => new CommentRow(
            $row['id'],
            $row['text'],
            new \DateTimeImmutable($row['date']),
            $row['author_name'],
            $row['email']
        ), $rows);
    }
}
