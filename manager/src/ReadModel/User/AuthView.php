<?php

declare(strict_types=1);

namespace App\ReadModel\User;

class AuthView
{
    public string $id;
    public string $email;
    public string $password_hash;
    public string $name;
    public string $role;
    public string $status;
    public string $date;
    public ?string $account_id = null;

    public function getAccountId(): ?string
    {
        return $this->account_id;
    }
}
