<?php

declare(strict_types=1);

namespace App\Controller;

use App\Infrastructure\Inertia\InertiaService;
use App\Model\User\Entity\User\User;
use App\Model\User\UseCase\Activate;
use App\Model\User\UseCase\Block;
use App\Model\User\UseCase\Create;
use App\Model\User\UseCase\Edit;
use App\Model\User\UseCase\Role;
use App\Model\User\UseCase\SignUp\Confirm;
use App\ReadModel\User\Filter;
use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users')]
#[IsGranted('ROLE_MANAGE_USERS')]
class UsersController extends AbstractController
{
    private const PER_PAGE = 10;

    public function __construct(private ErrorHandler $errors) {}

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
                'date' => (new \DateTimeImmutable($user['date']))->format('Y-m-d'),
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
    public function create(Request $request, Create\Handler $handler, InertiaService $inertia): Response
    {
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Users/Create');
        }

        $data = json_decode($request->getContent(), true);

        $errors = [];

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email.';
        }
        if (empty($data['firstName']) || mb_strlen($data['firstName']) < 2) {
            $errors['firstName'] = 'First name is required and must be at least 2 characters.';
        }
        if (empty($data['lastName']) || mb_strlen($data['lastName']) < 2) {
            $errors['lastName'] = 'Last name is required and must be at least 2 characters.';
        }

        if ($errors) {
            return $this->json(['errors' => $errors], 422);
        }

        $command = new Create\Command();
        $command->email = $data['email'];
        $command->firstName = $data['firstName'];
        $command->lastName = $data['lastName'];

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json(['errors' => ['general' => $e->getMessage()]], 400);
        }

        return $this->json(['message' => 'User created successfully']);
    }



    #[Route('/{id}/edit', name: 'users.edit', methods: ['GET', 'POST'])]
    public function edit(
        User $user,
        Request $request,
        Edit\Handler $handler,
        InertiaService $inertia
    ): Response {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            return $inertia->render($request, 'ErrorPage', [
                'flash' => ['error' => 'Unable to edit yourself.'],
            ]);
        }

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Users/Edit', [
                'user' => [
                    'id' => $user->getId()->getValue(),
                    'email' => $user->getEmail()->getValue(),
                    'firstName' => $user->getName()->getFirst(),
                    'lastName' => $user->getName()->getLast(),
                ],
            ]);
        }

        $data = json_decode($request->getContent(), true);
        $errors = [];

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email.';
        }
        if (empty($data['firstName']) || mb_strlen($data['firstName']) < 2) {
            $errors['firstName'] = 'First name is required and must be at least 2 characters.';
        }
        if (empty($data['lastName']) || mb_strlen($data['lastName']) < 2) {
            $errors['lastName'] = 'Last name is required and must be at least 2 characters.';
        }

        if ($errors) {
            return $this->json(['errors' => $errors], 422);
        }

        $command = new Edit\Command($user->getId()->getValue());
        $command->id = $user->getId()->getValue();
        $command->email = $data['email'];
        $command->firstName = $data['firstName'];
        $command->lastName = $data['lastName'];

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json(['errors' => ['general' => $e->getMessage()]], 400);
        }

        return $this->json(['message' => 'User updated successfully']);
    }



    #[Route('/{id}/role', name: 'users.role', methods: ['GET', 'POST'])]
    public function role(
        User $user,
        Request $request,
        Role\Handler $handler,
        InertiaService $inertia
    ): Response {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            return $inertia->render($request, 'ErrorPage', [
                'flash' => ['error' => 'Unable to change role for yourself.'],
            ]);
        }

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Users/Role', [
                'user' => [
                    'id' => $user->getId()->getValue(),
                    'email' => $user->getEmail()->getValue(),
                    'firstName' => $user->getName()->getFirst(),
                    'lastName' => $user->getName()->getLast(),
                    'roles' => $user->getRoles(),
                ],
                'availableRoles' => [
                    'ROLE_USER' => 'User',
                    'ROLE_MODERATOR' => 'Moderator',
                    'ROLE_ADMIN' => 'Administrator',
                ],
            ]);
        }

        $data = json_decode($request->getContent(), true);
        $errors = [];

        if (empty($data['role']) || !is_string($data['role'])) {
            $errors['role'] = 'You must select a role.';
        }

        if ($errors) {
            return $inertia->render($request, 'Users/Role', [
                'user' => [
                    'id' => $user->getId()->getValue(),
                    'email' => $user->getEmail()->getValue(),
                    'firstName' => $user->getName()->getFirst(),
                    'lastName' => $user->getName()->getLast(),
                    'roles' => $user->getRoles(),
                ],
                'availableRoles' => [
                    'ROLE_USER' => 'User',
                    'ROLE_MODERATOR' => 'Moderator',
                    'ROLE_ADMIN' => 'Administrator',
                ],
                'errors' => $errors,
            ])->setStatusCode(422);
        }

        $command = new Role\Command($user->getId()->getValue());
        $command->role = $data['role'];

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $inertia->render($request, 'Users/Role', [
                'user' => [
                    'id' => $user->getId()->getValue(),
                    'email' => $user->getEmail()->getValue(),
                    'firstName' => $user->getName()->getFirst(),
                    'lastName' => $user->getName()->getLast(),
                    'roles' => $user->getRoles(),
                ],
                'availableRoles' => [
                    'ROLE_USER' => 'User',
                    'ROLE_MODERATOR' => 'Moderator',
                    'ROLE_ADMIN' => 'Administrator',
                ],
                'flash' => ['error' => $e->getMessage()],
            ])->setStatusCode(400);
        }

        return new Response('', 303, [
            'X-Inertia' => 'true',
            'Location' => $this->generateUrl('users.show', ['id' => $user->getId()->getValue()]),
        ]);
    }



    #[Route('/{id}/confirm', name: 'users.confirm', methods: ['POST'])]
    public function confirm(User $user, Request $request, Confirm\Manual\Handler $handler): Response
    {
        $command = new Confirm\Manual\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    #[Route('/{id}/activate', name: 'users.activate', methods: ['POST'])]
    public function activate(User $user, Request $request, Activate\Handler $handler): Response
    {
        $command = new Activate\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    #[Route('/{id}/block', name: 'users.block', methods: ['POST'])]
    public function block(User $user, Request $request, Block\Handler $handler): Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to block yourself.');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Block\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    #[Route('/{id}', name: 'users.show', requirements: ['id' => '[0-9a-fA-F\-]{36}'], methods: ['GET'])]
    public function show(Request $request, User $user, InertiaService $inertia): Response
    {
        return $inertia->render($request, 'Users/Show', [
            'user' => [
                'id' => $user->getId()->getValue(),
                'name' => [
                    'first' => $user->getName()->getFirst(),
                    'last' => $user->getName()->getLast(),
                    'full' => $user->getName()->getFull(),
                ],
                'email' => $user->getEmail()?->getValue(),
                'date' => $user->getDate()->format('Y-m-d H:i:s'),
                'role' => $user->getRole()->getName(),
                'status' => $user->getStatus(),
                'networks' => array_map(fn($n) => [
                    'network' => $n->getNetwork(),
                    'identity' => $n->getIdentity(),
                ], $user->getNetworks()),
                'wait' => $user->isWait(),
                'active' => $user->isActive(),
                'blocked' => $user->isBlocked(),
            ],
        ]);
    }
}
