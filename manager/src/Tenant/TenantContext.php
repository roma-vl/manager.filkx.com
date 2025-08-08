<?php
declare(strict_types=1);

namespace App\Tenant;

use App\Model\User\Entity\Account\Account;

class TenantContext
{
    private ?Account $account = null;

    public function setAccount(Account $account): void { $this->account = $account; }
    public function getAccount(): ?Account { return $this->account; }
    public function hasAccount(): bool { return $this->account !== null; }
}
