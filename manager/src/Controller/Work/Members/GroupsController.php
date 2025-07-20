<?php

declare(strict_types=1);

namespace App\Controller\Work\Members;

use App\Controller\BaseController;
use App\Controller\ErrorHandler;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\UseCase\Members\Group\Create;
use App\Model\Work\UseCase\Members\Group\Edit;
use App\Model\Work\UseCase\Members\Group\Remove;
use App\ReadModel\Work\Members\GroupFetcher;
use App\Service\CommandFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/work/members/groups', name: 'work.members.groups')]
#[IsGranted('ROLE_WORK_MANAGE_MEMBERS')]
class GroupsController extends BaseController
{
    public function __construct(
        private readonly ErrorHandler $errors,
    ) {
    }

    #[Route('', name: '')]
    public function index(GroupFetcher $fetcher, InertiaService $inertia, Request $request): Response
    {
        return $inertia->render($request, 'Work/Members/Groups/Index', [
            'groups' => $fetcher->all(),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        Create\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
    ): Response {
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Members/Groups/Create');
        }

        $command = new Create\Command();
        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Members/Groups/Create', ['errors' => $errors]);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Групу створено успішно.');

            return $inertia->redirect('/work/members/groups');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->redirect('/work/members/groups/create');
        }
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(
        Group $group,
        Request $request,
        Edit\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
    ): Response {
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Members/Groups/Edit', [
                'group' => [
                    'id' => $group->getId()->getValue(),
                    'name' => $group->getName(),
                ],
            ]);
        }

        $command = Edit\Command::fromGroup($group);
        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Members/Groups/Edit', [
                'group' => [
                    'id' => $group->getId()->getValue(),
                    'name' => $group->getName(),
                ],
                'errors' => $errors,
            ]);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Групу оновлено.');

            return $inertia->redirect('/work/members/groups');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->redirect("/work/members/groups/{$group->getId()->getValue()}/edit");
        }
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(
        Group $group,
        Request $request,
        Remove\Handler $handler,
        InertiaService $inertia,
    ): Response {
        $command = new Remove\Command($group->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Групу видалено.');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $inertia->redirect('/work/members/groups');
    }

    #[Route('/{id}', name: '.show')]
    public function show(): Response
    {
        return $this->redirectToRoute('work.members.groups');
    }
}
