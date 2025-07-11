<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\User\Entity\User\User;
use App\ReadModel\NotFoundException;
use App\ReadModel\User\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(User::class);
        $this->paginator = $paginator;
    }

    public function existsByResetToken(string $token): bool
    {
        $result = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user_users')
            ->where('reset_token_token = :token')
            ->setParameter('token', $token)
            ->fetchOne();

        return (int) $result > 0;
    }

    public function findForAuthByEmail(string $email): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'email', 'password_hash', "TRIM(CONCAT(name_first, ' ', name_last)) AS name", 'role', 'status')
            ->from('user_users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery();

        $result = $stmt->fetchAssociative();

        if (!$result) {
            return null;
        }

        $authView = new AuthView();
        $authView->id = $result['id'];
        $authView->email = $result['email'];
        $authView->password_hash = $result['password_hash'];
        $authView->name = $result['name'];
        $authView->role = $result['role'];
        $authView->status = $result['status'];

        return $authView;
    }


    public function findForAuthByNetwork(string $network, string $identity): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('u.id', 'u.email', 'u.password_hash', "TRIM(CONCAT(u.name_first, ' ', u.name_last)) AS name", 'u.role', 'u.status')
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->where('n.network = :network AND n.identity = :identity')
            ->setParameter('network', $network)
            ->setParameter('identity', $identity)
            ->executeQuery();

        $result = $stmt->fetchAssociative();

        return $result ?: null;
    }

    public function findByEmail(string $email): ?ShortView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'email', 'role', 'status')
            ->from('user_users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery();

        $result = $stmt->fetchAssociative();

        return $result ?: null;
    }

    public function findBySignUpConfirmToken(string $token): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('confirm_token = :token')
            ->setParameter('token', $token)
            ->executeQuery();

        $result = $stmt->fetchAssociative();

        return $result ?: null;
    }

    public function findUserEntityBySignUpConfirmToken(string $token): ?User
    {
        return $this->repository->findOneBy(['confirmToken' => $token]);
    }



    public function get(string $id): User
    {
        if (!$user = $this->repository->find($id)) {
            throw new NotFoundException('User is not found');
        }
        return $user;
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('id', 'date', "TRIM(CONCAT(name_first, ' ', name_last)) AS name", 'email', 'role', 'status')
            ->from('user_users');

        if ($filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(name_first, \" \", name_last))', ':name'));
            $qb->setParameter('name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->email) {
            $qb->andWhere($qb->expr()->like('LOWER(email)', ':email'));
            $qb->setParameter('email', '%' . mb_strtolower($filter->email) . '%');
        }

        if ($filter->status) {
            $qb->andWhere('status = :status');
            $qb->setParameter('status', $filter->status);
        }

        if ($filter->role) {
            $qb->andWhere('role = :role');
            $qb->setParameter('role', $filter->role);
        }

        if (!\in_array($sort, ['date', 'name', 'email', 'role', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}
