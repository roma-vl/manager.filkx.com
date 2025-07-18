<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects;

use App\Controller\BaseController;
use App\Controller\ErrorHandler;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Role\Role;
use App\Model\Work\UseCase\Projects\Role\Copy;
use App\Model\Work\UseCase\Projects\Role\Create;
use App\Model\Work\UseCase\Projects\Role\Edit;
use App\Model\Work\UseCase\Projects\Role\Remove;
use App\ReadModel\Work\Projects\RoleFetcher;
use App\Service\CommandFactory;
use DomainException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/work/projects/roles', name: 'work.projects.roles')]
#[IsGranted('ROLE_WORK_MANAGE_PROJECTS')]
class RolesController extends BaseController
{
    public function __construct(
        private readonly ErrorHandler $errors,
    ) {}

    #[Route('', name: '', methods: ['GET'])]
    public function index(RoleFetcher $fetcher, InertiaService $inertia, Request $request): Response
    {
        return $inertia->render($request, 'Work/Projects/Roles/Index', [
            'roles' => $fetcher->all(),
            'permissions' => Permission::names(),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        Create\Handler $handler,
        InertiaService $inertia,
        CommandFactory $factory
    ): Response {
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Roles/Create', [
                'permissions' => Permission::names()
            ]);
        }

        $command = new Create\Command();
        $errors = $factory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Projects/Roles/Create', [
                'errors' => $errors,
                'permissions' => Permission::names(),
            ]);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Роль створено успішно.');
            return $inertia->redirect('/work/projects/roles');
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $inertia->redirect('/work/projects/roles/create');
        }
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(
        Role $role,
        Request $request,
        Edit\Handler $handler,
        InertiaService $inertia,
        CommandFactory $factory
    ): Response {
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Roles/Edit', [
                'role' => [
                    'id' => $role->getId()->getValue(),
                    'name' => $role->getName(),
                    'permissions' => array_map(
                        fn(Permission $permission) => $permission->getName(),
                        $role->getPermissions()
                    ),
                ],
                'permissions' => Permission::names(),
            ]);
        }

        $command = Edit\Command::fromRole($role);
        $errors = $factory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Projects/Roles/Edit', [
                'role' => [
                    'id' => $role->getId()->getValue(),
                ],
                'permissions' => Permission::names(),
                'errors' => $errors,
            ]);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Роль оновлено.');
            return $inertia->redirect('/work/projects/roles/' . $role->getId()->getValue());
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $inertia->redirect('/work/projects/roles/' . $role->getId()->getValue() . '/edit');
        }
    }

    #[Route('/{id}/copy', name: '.copy', methods: ['GET', 'POST'])]
    public function copy(
        Role $role,
        Request $request,
        Copy\Handler $handler,
        InertiaService $inertia,
        CommandFactory $factory
    ): Response {
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Roles/Copy', [
                'role' => [
                    'id' => $role->getId()->getValue(),
                    'name' => $role->getName(),
                    'permissions' => array_map(fn(Permission $p) => $p->getName(), $role->getPermissions()),
                ],
                'permissions' => Permission::names(),
            ]);
        }

        $command = new Copy\Command($role->getId()->getValue());
        $errors = $factory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Projects/Roles/Copy', [
                'role' => ['id' => $role->getId()->getValue()],
                'permissions' => Permission::names(),
                'errors' => $errors,
            ]);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Роль скопійовано.');
            return $inertia->redirect('/work/projects/roles');
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $inertia->redirect('/work/projects/roles/' . $role->getId()->getValue() . '/copy');
        }
    }


    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(
        Role $role,
        Request $request,
        Remove\Handler $handler
    ): Response {
        try {
            $handler->handle(new Remove\Command($role->getId()->getValue()));
            $this->addFlash('success', 'Роль видалено.');
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.roles');
    }

    #[Route('/{id}', name: '.show', methods: ['GET'])]
    public function show(Role $role, InertiaService $inertia, Request $request): Response
    {
        return $inertia->render($request, 'Work/Projects/Roles/Show', [
            'role' => [
                'id' => $role->getId()->getValue(),
                'name' => $role->getName(),
                'permissions' => array_map(
                    fn(Permission $permission) => $permission->getName(),
                    $role->getPermissions()
                ),
            ],
        ]);
    }
}
