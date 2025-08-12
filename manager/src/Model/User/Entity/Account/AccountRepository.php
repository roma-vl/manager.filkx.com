<?php

declare(strict_types=1);

namespace App\Model\User\Entity\Account;

use Doctrine\ORM\EntityManagerInterface;

class AccountRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function add(Account $account): void
    {
        $this->em->persist($account);
    }
}
