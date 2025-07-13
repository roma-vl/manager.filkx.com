<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    private EntityManagerInterface $em;
    /** @var EntityRepository<User> */
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    public function findByConfirmToken(string $token): ?User
    {
        /** @var User|null $user */
        $user = $this->repo->findOneBy(['confirmToken' => $token]);

        return $user;
    }

    public function findByResetToken(string $token): ?User
    {
        /** @var User|null $user */
        $user = $this->repo->findOneBy(['resetToken.token' => $token]);

        return $user;
    }

    public function get(Id $id): User
    {
        /** @var User|null $user */
        $user = $this->repo->find($id->getValue());
        if (!$user) {
            throw new EntityNotFoundException('User is not found.');
        }

        return $user;
    }

    public function getByEmail(Email $email): User
    {
        /** @var User|null $user */
        $user = $this->repo->findOneBy(['email' => $email->getValue()]);
        if (!$user) {
            throw new EntityNotFoundException('User is not found.');
        }

        return $user;
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.email = :email')
                ->setParameter(':email', $email->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->innerJoin('t.networks', 'n')
                ->andWhere('n.network = :network and n.identity = :identity')
                ->setParameter(':network', $network)
                ->setParameter(':identity', $identity)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}
