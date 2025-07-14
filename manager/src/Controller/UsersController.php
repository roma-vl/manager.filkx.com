<?php

declare(strict_types=1);

namespace App\Controller;

use App\Infrastructure\Inertia\InertiaService;
use App\Model\EntityNotFoundException;
use App\Model\User\Entity\User\User;
use App\Model\User\UseCase\Activate;
use App\Model\User\UseCase\Block;
use App\Model\User\UseCase\Create;
use App\Model\User\UseCase\Edit;
use App\Model\User\UseCase\Role;
use App\Model\User\UseCase\SignUp\Confirm;
use App\ReadModel\Props\Users\UserPropsProvider;
use App\ReadModel\Props\Users\UserShowPropsProvider;
use App\ReadModel\User\Filter;
use App\ReadModel\User\UserFetcher;
use App\Service\CommandFactory;
use DateTimeImmutable;
use DomainException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users')]
#[IsGranted('ROLE_MANAGE_USERS')]
class UsersController extends BaseController
{
    private const PER_PAGE = 10;

    public function __construct(
        private readonly UserPropsProvider $userPropsProvider,
        private readonly UserShowPropsProvider $userShowPropsProvider,
    ) {}

    #[Route('', name: 'users', methods: ['GET'])]
    public function index(Request $request, UserFetcher $fetcher, InertiaService $inertia): Response
    {
        $filter = new Filter\Filter();
        $filter->name = $request->query->get('name');
        $filter->email = $request->query->get('email');
        $filter->role = $request->query->get('role');
        $filter->status = $request->query->get('status');

        $pagination = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'date'),
            $request->query->get('direction', 'desc')
        );

        return $inertia->render($request, 'Users/Index', [
            'users' => array_map(fn($user) => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'status' => $user['status'],
                'date' => (new DateTimeImmutable($user['date']))->format('Y-m-d'),
            ], $pagination->getItems()),
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'lastPage' => ceil($pagination->getTotalItemCount() / self::PER_PAGE),
                'total' => $pagination->getTotalItemCount(),
            ],
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 'date'),
            'direction' => $request->query->get('direction', 'desc'),

        ]);
    }

    #[Route('/create', name: 'users.create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        Create\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
    ): Response
    {
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Users/Create');
        }

        $command = new Create\Command();
        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $this->renderWithErrors($request, $inertia, 'Users/Create', $errors);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'User created successfully.');
            return $inertia->redirect('/users');
        } catch (EntityNotFoundException|DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            return $inertia->redirect('/users/create');
        }
    }

    #[Route('/{id}/edit', name: 'users.edit', methods: ['GET', 'POST'])]
    public function edit(
        User $user,
        Request $request,
        Edit\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
    ): Response {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Не можна редагувати себе');
            return $inertia->redirect('/users/' . $user->getId()->getValue() . '/edit');
        }

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Users/Edit',
                $this->userPropsProvider->getProps(['userId' => $user->getId()->getValue()])
            );
        }

        $command = new Edit\Command($user->getId()->getValue());;
        $errors = $commandFactory->createFromRequest($request, $command);

        //@TODO "Костиль" — прибираємо помилку, якщо email не змінився
        if ($errors && isset($errors['email']) && $user->getEmail()->getValue() === $command->email) {
            unset($errors['email']);
        }

        if ($errors) {
            return $this->renderWithErrors(
                $request,
                $inertia,
                'Users/Edit',
                $errors,
                $this->userPropsProvider->getProps(['userId' => $user->getId()->getValue()])
            );
        }
        try {
            $handler->handle($command);
            $this->addFlash('success', 'User created successfully.');
            return $inertia->redirect('/users/' . $user->getId()->getValue() );
        } catch (EntityNotFoundException|DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            return $inertia->redirect('/users/' . $user->getId()->getValue() . '/edit');
        }
    }

    #[Route('/{id}/role', name: 'users.role', methods: ['GET', 'POST'])]
    public function role(
        User $user,
        Request $request,
        Role\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
    ): Response {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Не можна редагувати собі роль');
            return $inertia->redirect('/users/' . $user->getId()->getValue() . '/role');
        }

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Users/Role', array_merge(
                $this->userPropsProvider->getProps(['userId' => $user->getId()->getValue()]),
                ['availableRoles' => $user->getRolesList()]
            ));
        }

        $command = new Role\Command($user->getId()->getValue());;
        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Users/Role', array_merge(
                $this->userPropsProvider->getProps(['userId' => $user->getId()->getValue()]),
                ['availableRoles' => $user->getRolesList()],
                ['errors' => $errors],
            ))->setStatusCode(422);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'User role changed successfully.');
            return $inertia->redirect('/users/' . $user->getId()->getValue() );
        } catch (EntityNotFoundException|DomainException $e) {
            return $inertia->render($request, 'Users/Role', array_merge(
                $this->userPropsProvider->getProps(['userId' => $user->getId()->getValue()]),
                ['availableRoles' => $user->getRolesList()],
                ['flash' => ['error' => $e->getMessage()]],
            ))->setStatusCode(400);
        }
    }

    #[Route('/{id}/confirm', name: 'users.confirm', methods: ['POST'])]
    public function confirm(User $user, Request $request, Confirm\Manual\Handler $handler, InertiaService $inertia,): Response
    {
        $command = new Confirm\Manual\Command($user->getId()->getValue());
        try {
            $handler->handle($command);
            $this->addFlash('success', 'Підтверджено.');
            return $inertia->redirect('/users/' . $user->getId()->getValue() );
        } catch (EntityNotFoundException|DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            return $inertia->redirect('/users/' . $user->getId()->getValue());
        }
    }

    #[Route('/{id}/activate', name: 'users.activate', methods: ['POST'])]
    public function activate(User $user, Request $request, Activate\Handler $handler, InertiaService $inertia,): Response
    {
        $command = new Activate\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Активовано.');
            return $inertia->redirect('/users/' . $user->getId()->getValue() );
        } catch (EntityNotFoundException|DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            return $inertia->redirect('/users/' . $user->getId()->getValue());
        }
    }

    #[Route('/{id}/block', name: 'users.block', methods: ['POST'])]
    public function block(User $user, Request $request, Block\Handler $handler, InertiaService $inertia,): Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to block yourself.');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Block\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Заблоковано.');
            return $inertia->redirect('/users/' . $user->getId()->getValue() );
        } catch (EntityNotFoundException|DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            return $inertia->redirect('/users/' . $user->getId()->getValue());
        }
    }

    #[Route('/{id}', name: 'users.show', requirements: ['id' => '[0-9a-fA-F\-]{36}'], methods: ['GET'])]
    public function show(Request $request, User $user, InertiaService $inertia): Response
    {
        return $inertia->render($request, 'Users/Show',
            $this->userShowPropsProvider->getProps(['userId' => $user->getId()->getValue()]),
        );
    }
}
