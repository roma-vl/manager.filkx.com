<?php

declare(strict_types=1);

namespace App\Controller\Work\Members;

use App\Annotation\Guid;
use App\Controller\BaseController;
use App\Controller\ErrorHandler;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\UseCase\Members\Member\Archive;
use App\Model\Work\UseCase\Members\Member\Create;
use App\Model\Work\UseCase\Members\Member\Edit;
use App\Model\Work\UseCase\Members\Member\Move;
use App\Model\Work\UseCase\Members\Member\Reinstate;
use App\ReadModel\Work\Members\GroupFetcher;
use App\ReadModel\Work\Members\Member\Filter\Filter;
use App\ReadModel\Work\Members\Member\MemberFetcher;
use App\Service\CommandFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/work/members', name: 'work.members')]
#[IsGranted('ROLE_WORK_MANAGE_MEMBERS')]
class MembersController extends BaseController
{
    const PER_PAGE = 2;

    public function __construct(
        private readonly ErrorHandler $errors,
    ) {
    }

    #[Route('', name: 'work.members', methods: ['GET'])]
    public function index(
        Request $request,
        MemberFetcher $fetcher,
        GroupFetcher $groupFetcher,
        InertiaService $inertia,
    ): Response {
        $filter = new Filter();
        $filter->name = $request->query->get('name');
        $filter->email = $request->query->get('email');
        $filter->group = $request->query->get('group');
        $filter->status = $request->query->get('status');

        $pagination = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'asc')
        );

        return $inertia->render($request, 'Work/Members/Members/Index', [
            'members' => array_map(fn ($member) => [
                'id' => $member['id']->getValue(),
                'name' => $member['name'],
                'email' => $member['email']->getValue(),
                'group' => $member['group'],
                'status' => $member['status']->getName(),
                'memberships_count' => $member['memberships_count'] ?? 0,
            ], $pagination->getItems()),
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'lastPage' => ceil($pagination->getTotalItemCount() / self::PER_PAGE),
                'total' => $pagination->getTotalItemCount(),
            ],
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 'name'),
            'direction' => $request->query->get('direction', 'asc'),
            'groups' => array_map(fn ($id, $name) => ['id' => $id, 'name' => $name], array_keys($groupFetcher->assoc()), $groupFetcher->assoc()),
            'statuses' => [
                ['id' => 'active', 'name' => 'Active'],
                ['id' => 'archived', 'name' => 'Archived'],
            ],
        ]);
    }

    #[Route('/create/{id}', name: '.create', methods: ['GET', 'POST'])]
    public function create(
        User $user,
        Request $request,
        MemberFetcher $fetcher,
        GroupFetcher $groupFetcher,
        Create\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
    ): Response {
        $dd = $user->getAccount()->getId();
        if ($fetcher->exists($user->getId()->getValue())) {
            $this->addFlash('error', 'Учасник уже існує.');

            return $inertia->redirect('/users/' . $user->getId()->getValue());
        }
        $groups = $groupFetcher->assoc();
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Members/Members/Create', [
                'user' => [
                    'id' => $user->getId()->getValue(),
                    'firstName' => $user->getName()->getFirst(),
                    'lastName' => $user->getName()->getLast(),
                    'email' => $user->getEmail()?->getValue(),
                ],
                'groups' => $groups,
            ]);
        }

        $command = new Create\Command($user->getId()->getValue());
        $command->firstName = $user->getName()->getFirst();
        $command->lastName = $user->getName()->getLast();
        $command->email = $user->getEmail()?->getValue();

        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Members/Members/Create', [
                'user' => [
                    'id' => $user->getId()->getValue(),
                    'name' => $user->getName()->getFull(),
                    'email' => $command->email,
                ],
                'groups' => $groups,
                'errors' => $errors,
            ]);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Учасника додано.');

            return $inertia->redirect('/work/members/' . $user->getId()->getValue());
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->redirect('/work/members/' . $user->getId()->getValue());
        }
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(
        Member $member,
        Request $request,
        Edit\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
    ): Response {
        $command = Edit\Command::fromMember($member);

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Members/Members/Edit', [
                'member' => [
                    'id' => $member->getId()->getValue(),
                    'firstName' => $command->firstName,
                    'lastName' => $command->lastName,
                    'email' => $command->email,
                ],
            ]);
        }

        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Members/Members/Edit', [
                'errors' => $errors,
            ]);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Учасника оновлено.');

            return $inertia->redirect('/work/members/' . $member->getId()->getValue());
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->redirect('/work/members/' . $member->getId()->getValue() . '/edit');
        }
    }

    #[Route('/{id}/move', name: '.move', methods: ['GET', 'POST'])]
    public function move(
        Member $member,
        Request $request,
        Move\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
        GroupFetcher $groupFetcher,
    ): Response {
        $command = Move\Command::fromMember($member);
        $groups = $groupFetcher->assoc();
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Members/Members/Move', [
                'member' => [
                    'id' => $member->getId()->getValue(),
                    'group_id' => $member->getGroup()->getId()->getValue(),
                ],
                'groups' => $groups,
            ]);
        }

        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Members/Members/Move', [
                'errors' => $errors,
                'member' => [
                    'id' => $member->getId()->getValue(),
                ],
                'groups' => $groups,
            ]);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Учасника переміщено.');

            return $inertia->redirect('/work/members/' . $member->getId()->getValue());
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->redirect('/work/members/' . $member->getId()->getValue() . '/move');
        }
    }

    #[Route('/{id}/archive', name: '.archive', methods: ['POST'])]
    public function archive(Member $member, Archive\Handler $handler, InertiaService $inertia): Response
    {
        $command = new Archive\Command($member->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Учасника архівовано.');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $inertia->redirect('/work/members/' . $member->getId()->getValue());
    }

    #[Route('/{id}/reinstate', name: '.reinstate', methods: ['POST'])]
    public function reinstate(Member $member, Reinstate\Handler $handler, InertiaService $inertia): Response
    {
        if ($member->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Не можна активувати самого себе.');

            return $inertia->redirect('/work/members/' . $member->getId()->getValue());
        }

        $command = new Reinstate\Command($member->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Учасника активовано.');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $inertia->redirect('/work/members/' . $member->getId()->getValue());
    }

    #[Route('/{id}', name: '.show', requirements: ['id' => Guid::UUID_REGEX], methods: ['GET'])]
    public function show(Request $request, Member $member, InertiaService $inertia): Response
    {
        return $inertia->render($request, 'Work/Members/Members/Show', [
            'member' => [
                'id' => $member->getId()->getValue(),
                'name' => [
                    'full' => $member->getName()->getFull(),
                ],
                'email' => [
                    'value' => $member->getEmail()->getValue(),
                ],
                'group' => [
                    'name' => $member->getGroup()->getName(),
                ],
                'status' => [
                    'name' => $member->getStatus()->getName(),
                    'label' => $member->getStatus()->isActive() ? 'Active' : 'Archived',
                ],
                'active' => $member->getStatus()->isActive(),
                'archived' => !$member->getStatus()->isActive(),
            ],
            //            'departments' => $departments,
        ]);
    }
}
