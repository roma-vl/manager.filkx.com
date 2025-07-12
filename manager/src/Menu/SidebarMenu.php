<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SidebarMenu
{
    private FactoryInterface $factory;
    private AuthorizationCheckerInterface $auth;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $auth)
    {
        $this->factory = $factory;
        $this->auth = $auth;
    }

    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => ['class' => 'nav flex-column'],
        ]);

        $menu->addChild('Dashboard', [
            'route' => 'home',
        ])
            ->setExtra('icon', 'bi bi-speedometer2') // Bootstrap Icon
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link d-flex align-items-center gap-2');

        $menu->addChild('Work')
            ->setAttribute('class', 'nav-title mt-3 px-3 text-uppercase text-muted small fw-bold')
            ->setAttribute('disabled', true);

        return $menu;
    }
}
