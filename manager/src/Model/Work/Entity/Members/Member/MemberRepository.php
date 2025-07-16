<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Member;

use App\Model\EntityNotFoundException;
use App\Model\Work\Entity\Members\Group\Id as GroupId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class MemberRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Member::class);
    }

    public function has(Id $id): bool
    {
        return (int) $this->repo->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.id = :id')
                ->setParameter('id', $id) // якщо type кастомний – працюватиме
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function hasByGroup(GroupId $groupId): bool
    {
        return (int) $this->repo->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.group = :group')
                ->setParameter('group', $groupId)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function get(Id $id): Member
    {
        $member = $this->repo->find($id);

        if (!$member instanceof Member) {
            throw new EntityNotFoundException('Member is not found.');
        }

        return $member;
    }

    public function add(Member $member): void
    {
        $this->em->persist($member);
    }
}
