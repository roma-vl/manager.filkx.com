<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Members;

use App\Model\Work\Entity\Members\Group\Group;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

readonly class GroupFetcher
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws Exception
     */
    public function assoc(): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('g.id', 'g.name')
            ->from(Group::class, 'g')
            ->orderBy('g.name', 'ASC');

        $results = $qb->getQuery()->getArrayResult();

        $assoc = [];
        foreach ($results as $row) {
            $assoc[$row['id']->getValue()] = $row['name'];
        }

        return $assoc;
    }

    public function all(): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('g.id', 'g.name', 'COUNT(m.id) AS members')
            ->from(Group::class, 'g')
            ->leftJoin('g.members', 'm')
            ->groupBy('g.id')
            ->orderBy('g.name', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }
}
