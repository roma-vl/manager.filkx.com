<?php

declare(strict_types=1);

namespace App\Tenant;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TenantRequestListener
{
    public function __construct(
        private readonly TenantContext $tenantContext,
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Якщо немає авторизації — нічого не робимо
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        // $user має мати getAccount()
        $account = $user->getAccount();
        if (!$account) {
            return;
        }

        $this->tenantContext->setAccount($account);

        $filters = $this->em->getFilters();
        if (!$filters->isEnabled('account_filter')) {
            $filters->enable('account_filter');
        }

        // Параметр має бути рядком, getParameter буде правильно екранувати
        $filters->getFilter('account_filter')->setParameter('account_id', $account->getId());
    }
}
