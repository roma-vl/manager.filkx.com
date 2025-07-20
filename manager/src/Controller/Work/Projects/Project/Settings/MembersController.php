<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\Guid;
use App\Controller\ErrorHandler;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Projects\Project\Membership as MembershipModel;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\UseCase\Projects\Project\Membership;
use App\ReadModel\Work\Members\Member\MemberFetcher;
use App\ReadModel\Work\Projects\Project\DepartmentFetcher;
use App\ReadModel\Work\Projects\RoleFetcher;
use App\Security\Voter\Work\Projects\ProjectAccess;
use App\Service\CommandFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work/projects/{id}/settings/members', name: 'work.projects.project.settings.members')]
class MembersController extends AbstractController
{
    public function __construct(
        private readonly ErrorHandler $errors,
    ) {
    }

    #[Route('', name: '')]
    public function index(
        Project $project,
        InertiaService $inertia,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);
        $ff = $project->getMemberships();

        return $inertia->render($request, 'Work/Projects/Project/Settings/Members/Index', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
            ],
            'memberships' => array_map(static function (MembershipModel $membership) {
                return [
                    'member' => [
                        'id' => $membership->getMember()->getId()->getValue(),
                        'name' => [
                            'full' => $membership->getMember()->getName()->getFull(),
                        ],
                    ],
                    'departments' => array_map(static function ($department) {
                        return [
                            'id' => $department->getId()->getValue(),
                            'name' => $department->getName(),
                        ];
                    }, $membership->getDepartments()),
                    'roles' => array_map(static function ($role) {
                        return [
                            'id' => $role->getId()->getValue(),
                            'name' => $role->getName(),
                        ];
                    }, $membership->getRoles()),
                ];
            }, $project->getMemberships()),
        ]);
    }

    #[Route('/assign', name: '.assign', methods: ['GET', 'POST'])]
    public function assign(
        Project $project,
        Request $request,
        Membership\Add\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
        RoleFetcher $roleFetcher,
        DepartmentFetcher $departmentFetcher,
        MemberFetcher $memberFetcher,
    ): Response {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Project/Settings/Members/Assign', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'roles' => $roleFetcher->allList(),
                'departments' => $departmentFetcher->listOfProject($project->getId()->getValue()),
                'members' => $this->mapMembers($memberFetcher->activeGroupedList()),
            ]);
        }

        $command = new Membership\Add\Command($project->getId()->getValue());
        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Projects/Project/Settings/Members/Assign', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'errors' => $errors,
            ]);
        }

        try {
            $handler->handle($command);

            return $this->redirectToRoute('work.projects.project.settings.members', ['id' => $project->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);

            return $inertia->render($request, 'Work/Projects/Project/Settings/Members/Assign', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'errors' => ['message' => $e->getMessage()],
            ]);
        }
    }

    private function mapMembers(array $list): array
    {
        $result = [];

        foreach ($list as $item) {
            $result[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'group' => $item['group'],
            ];
        }

        return $result;
    }

    #[Route('/{member_id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(
        Project $project,
        string $member_id,
        Request $request,
        Membership\Edit\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
        RoleFetcher $roleFetcher,
        DepartmentFetcher $departmentFetcher,
        MemberFetcher $memberFetcher,
    ): Response {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        $membership = $project->getMembership(new Id($member_id));

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Project/Settings/Members/Edit', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'roles' => $roleFetcher->allList(),
                'departments' => $departmentFetcher->listOfProject($project->getId()->getValue()),
                'membership' => [
                    'id' => $membership->getMember()->getId()->getValue(),
                    'departments' => array_map(
                        fn ($d) => $d->getId()->getValue(),
                        $membership->getDepartments()
                    ),
                    'roles' => array_map(
                        fn ($r) => $r->getId()->getValue(),
                        $membership->getRoles()
                    ),
                ],
            ]);
        }

        $command = Membership\Edit\Command::fromMembership($project, $membership);
        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Projects/Project/Settings/Members/Edit', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'roles' => $roleFetcher->allList(),
                'departments' => $departmentFetcher->listOfProject($project->getId()->getValue()),
                'membership' => [
                    'id' => $membership->getMember()->getId()->getValue(),
                    'departments' => array_map(
                        fn ($d) => $d->getId()->getValue(),
                        $membership->getDepartments()
                    ),
                    'roles' => array_map(
                        fn ($r) => $r->getId()->getValue(),
                        $membership->getRoles()
                    ),
                ],
                'errors' => $errors,
            ]);
        }

        try {
            $handler->handle($command);

            return $this->redirectToRoute('work.projects.project.settings.members', ['id' => $project->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);

            return $inertia->render($request, 'Work/Projects/Project/Settings/Members/Edit', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'roles' => $roleFetcher->allList(),
                'departments' => $departmentFetcher->listOfProject($project->getId()->getValue()),
                'membership' => [
                    'id' => $membership->getMember()->getId()->getValue(),
                    'departments' => array_map(
                        fn ($d) => $d->getId()->getValue(),
                        $membership->getDepartments()
                    ),
                    'roles' => array_map(
                        fn ($r) => $r->getId()->getValue(),
                        $membership->getRoles()
                    ),
                ],
                'errors' => ['message' => $e->getMessage()],
            ]);
        }
    }

    #[Route('/{member_id}/revoke', name: '.revoke', methods: ['POST'])]
    public function revoke(
        Project $project,
        string $member_id,
        Request $request,
        Membership\Remove\Handler $handler,
    ): Response {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        //        if (!$this->isCsrfTokenValid('revoke', $request->request->get('token'))) {
        //            return $this->redirectToRoute('work.projects.project.settings.members', ['id' => $project->getId()]);
        //        }

        $command = new Membership\Remove\Command($project->getId()->getValue(), $member_id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings.members', ['id' => $project->getId()]);
    }

    #[Route('/{member_id}', name: '.show', requirements: ['member_id' => Guid::UUID_REGEX])]
    public function show(Project $project): Response
    {
        return $this->redirectToRoute('work.projects.project.settings.members', ['id' => $project->getId()]);
    }
}
