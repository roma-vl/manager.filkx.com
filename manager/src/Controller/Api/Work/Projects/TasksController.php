<?php

declare(strict_types=1);

namespace App\Controller\Api\Work\Projects;

use App\Controller\Api\PaginationSerializer;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Task\File\File;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\UseCase\Projects\Task\Plan;
use App\ReadModel\Work\Projects\Action\ActionFetcher;
use App\ReadModel\Work\Projects\Action\Feed\Feed;
use App\ReadModel\Work\Projects\Action\Feed\Item;
use App\ReadModel\Work\Projects\Task\CommentFetcher;
use App\ReadModel\Work\Projects\Task\Filter;
use App\ReadModel\Work\Projects\Task\TaskFetcher;
use App\Security\Voter\Work\Projects\TaskAccess;
use App\Service\Uploader\FileUploader;
use App\Service\Work\Processor\Processor;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/work/tasks', name: 'work.projects.tasks')]
#[OA\Tag(name: 'Work Task')]
class TasksController extends AbstractController
{
    private const PER_PAGE = 10;

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly DenormalizerInterface $denormalizer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('', name: '', methods: ['GET'])]
    #[OA\Get(
        path: '/work/tasks',
        summary: 'Get list of tasks with filters',
        security: [['oauth2' => ['common']]],
        parameters: [
            new OA\QueryParameter(name: 'filter[author]', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'filter[text]', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'filter[type]', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'filter[status]', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'filter[priority]', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'filter[executor]', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'filter[roots]', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'page', schema: new OA\Schema(type: 'integer')),
            new OA\QueryParameter(name: 'sort', schema: new OA\Schema(type: 'string')),
            new OA\QueryParameter(name: 'direction', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful response',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'items',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer'),
                                    new OA\Property(property: 'project', properties: [
                                        new OA\Property(property: 'id', type: 'string'),
                                        new OA\Property(property: 'name', type: 'string'),
                                    ],
                                        type: 'object'
                                    ),
                                    new OA\Property(property: 'author', properties: [
                                        new OA\Property(property: 'id', type: 'string'),
                                        new OA\Property(property: 'name', type: 'string'),
                                    ],
                                        type: 'object'
                                    ),
                                    new OA\Property(property: 'date', type: 'string'),
                                    new OA\Property(property: 'plan_date', type: 'string', nullable: true),
                                    new OA\Property(property: 'parent', type: 'string', nullable: true),
                                    new OA\Property(property: 'name', type: 'string'),
                                    new OA\Property(property: 'type', type: 'string'),
                                    new OA\Property(property: 'progress', type: 'integer'),
                                    new OA\Property(property: 'priority', type: 'integer'),
                                    new OA\Property(property: 'status', type: 'string'),
                                    new OA\Property(property: 'executors', type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: 'name', type: 'string'),
                                            ],
                                            type: 'object'
                                        )
                                    ),
                                ]
                            )
                        ),
                        new OA\Property(property: 'pagination', ref: '#/components/schemas/Pagination'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function index(Request $request, TaskFetcher $fetcher): Response
    {
        $filter = $this->isGranted('ROLE_WORK_MANAGE_PROJECTS')
            ? Filter\Filter::all()
            : Filter\Filter::all()->withMember($this->getUser()->getId());

        /** @var Filter\Filter $filter */
        $filter = $this->denormalizer->denormalize(
            $request->query->get('filter', []),
            Filter\Filter::class,
            'array',
            [
                'object_to_populate' => $filter,
                'ignored_attributes' => ['member', 'project'],
            ]
        );

        $pagination = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort'),
            $request->query->get('direction')
        );

        return $this->json([
            'items' => array_map(static fn (array $item) => [
                'id' => $item['id'],
                'project' => [
                    'id' => $item['project_id'],
                    'name' => $item['project_name'],
                ],
                'author' => [
                    'id' => $item['author_id'],
                    'name' => $item['author_name'],
                ],
                'date' => $item['date'],
                'plan_date' => $item['plan_date'],
                'parent' => $item['parent'],
                'name' => $item['name'],
                'type' => $item['type'],
                'progress' => $item['progress'],
                'priority' => $item['priority'],
                'status' => $item['status'],
                'executors' => array_map(static fn (array $member) => [
                    'name' => $member['name'],
                ], $item['executors']),
            ], (array) $pagination->getItems()),
            'pagination' => PaginationSerializer::toArray($pagination),
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}/plan', name: '.plan', methods: ['PUT'])]
    #[OA\Put(
        path: '/work/tasks/{id}/plan',
        summary: 'Set plan date for a task',
        security: [['oauth2' => ['common']]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['date'],
                properties: [
                    new OA\Property(property: 'date', type: 'string'),
                ]
            )
        ),
        parameters: [
            new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Plan date set successfully'),
            new OA\Response(response: 400, description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorModel')
            ),
        ]
    )]
    public function plan(Task $task, Request $request, Plan\Set\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $data = json_decode($request->getContent(), true);

        $command = new Plan\Set\Command($this->getUser()->getId(), $task->getId()->getValue());
        $command->date = !empty($data['date']) ? new \DateTimeImmutable($data['date']) : null;

        $violations = $this->validator->validate($command);

        if (\count($violations) > 0) {
            $json = $this->serializer->serialize($violations, 'json');

            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        return $this->json([]);
    }

    #[Route('/{id}/plan', name: '.plan.delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/work/tasks/{id}/plan',
        summary: 'Remove plan date from a task',
        security: [['oauth2' => ['common']]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Plan date removed successfully'),
            new OA\Response(response: 400, description: 'Error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorModel')
            ),
        ]
    )]
    public function removePlan(Task $task, Plan\Remove\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Plan\Remove\Command($this->getUser()->getId(), $task->getId()->getValue());
        $handler->handle($command);

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: '.show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[OA\Get(
        path: '/work/tasks/{id}',
        summary: 'Get task details by ID',
        security: [['oauth2' => ['common']]],
        parameters: [
            new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'integer', format: 'int64')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'project', properties: [
                            new OA\Property(property: 'id', type: 'string'),
                            new OA\Property(property: 'name', type: 'string'),
                        ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'author', properties: [
                            new OA\Property(property: 'id', type: 'string'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'avatar', type: 'string'),
                        ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'date', type: 'string'),
                        new OA\Property(property: 'plan_date', type: 'string', nullable: true),
                        new OA\Property(property: 'start_date', type: 'string', nullable: true),
                        new OA\Property(property: 'end_date', type: 'string', nullable: true),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'content', type: 'string'),
                        new OA\Property(property: 'files', type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'string'),
                                    new OA\Property(property: 'date', type: 'string'),
                                    new OA\Property(property: 'member', properties: [
                                        new OA\Property(property: 'id', type: 'string'),
                                        new OA\Property(property: 'name', type: 'string'),
                                    ],
                                        type: 'object'
                                    ),
                                    new OA\Property(property: 'info', properties: [
                                        new OA\Property(property: 'url', type: 'string'),
                                        new OA\Property(property: 'name', type: 'string'),
                                        new OA\Property(property: 'size', type: 'integer'),
                                    ],
                                        type: 'object'
                                    ),
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(property: 'type', type: 'string'),
                        new OA\Property(property: 'progress', type: 'integer'),
                        new OA\Property(property: 'priority', type: 'integer'),
                        new OA\Property(property: 'parent', properties: [
                            new OA\Property(property: 'id', type: 'string'),
                            new OA\Property(property: 'name', type: 'string'),
                        ], type: 'object',
                            nullable: true
                        ),
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(property: 'executors', type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'string'),
                                    new OA\Property(property: 'name', type: 'string'),
                                    new OA\Property(property: 'avatar', type: 'string'),
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(property: 'feed', type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'date', type: 'string'),
                                    new OA\Property(property: 'action', properties: [
                                        new OA\Property(property: 'id', type: 'string'),
                                        new OA\Property(property: 'date', type: 'string'),
                                        new OA\Property(property: 'actor', properties: [
                                            new OA\Property(property: 'id', type: 'string'),
                                            new OA\Property(property: 'name', type: 'string'),
                                        ],
                                            type: 'object'
                                        ),
                                        new OA\Property(property: 'set', properties: [
                                            new OA\Property(property: 'project', properties: [
                                                new OA\Property(property: 'id', type: 'string'),
                                                new OA\Property(property: 'name', type: 'string'),
                                            ], type: 'object',
                                                nullable: true
                                            ),
                                            new OA\Property(property: 'name', type: 'string', nullable: true),
                                            new OA\Property(property: 'content', type: 'string', nullable: true),
                                            new OA\Property(property: 'file', type: 'string', nullable: true),
                                            new OA\Property(property: 'removed_file', type: 'string', nullable: true),
                                            new OA\Property(property: 'parent', type: 'string', nullable: true),
                                            new OA\Property(property: 'removed_parent', type: 'boolean', nullable: true),
                                            new OA\Property(property: 'type', type: 'string', nullable: true),
                                            new OA\Property(property: 'status', type: 'string', nullable: true),
                                            new OA\Property(property: 'progress', type: 'integer', nullable: true),
                                            new OA\Property(property: 'priority', type: 'integer', nullable: true),
                                            new OA\Property(property: 'plan', type: 'string', nullable: true),
                                            new OA\Property(property: 'removed_plan', type: 'boolean', nullable: true),
                                            new OA\Property(property: 'executor', properties: [
                                                new OA\Property(property: 'id', type: 'string'),
                                                new OA\Property(property: 'name', type: 'string'),
                                            ], type: 'object',
                                                nullable: true
                                            ),
                                            new OA\Property(property: 'revoked_executor', properties: [
                                                new OA\Property(property: 'id', type: 'string'),
                                                new OA\Property(property: 'name', type: 'string'),
                                            ], type: 'object',
                                                nullable: true
                                            ),
                                        ],
                                            type: 'object'
                                        ),
                                    ], type: 'object',
                                        nullable: true
                                    ),
                                    new OA\Property(property: 'comment', properties: [
                                        new OA\Property(property: 'id', type: 'string'),
                                        new OA\Property(property: 'date', type: 'string'),
                                        new OA\Property(property: 'author', properties: [
                                            new OA\Property(property: 'id', type: 'string'),
                                            new OA\Property(property: 'name', type: 'string'),
                                            new OA\Property(property: 'avatar', type: 'string'),
                                        ],
                                            type: 'object'
                                        ),
                                        new OA\Property(property: 'content', type: 'string'),
                                    ], type: 'object',
                                        nullable: true
                                    ),
                                ],
                                type: 'object'
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function show(
        Task $task,
        CommentFetcher $comments,
        FileUploader $uploader,
        ActionFetcher $actions,
        Processor $processor,
    ): Response {
        $this->denyAccessUnlessGranted(TaskAccess::VIEW, $task);

        $feed = new Feed(
            $actions->allForTask($task->getId()->getValue()),
            $comments->allForTask($task->getId()->getValue()),
            $processor
        );

        return $this->json([
            'id' => $task->getId()->getValue(),
            'project' => [
                'id' => $task->getProject()->getId()->getValue(),
                'name' => $task->getProject()->getName(),
            ],
            'author' => [
                'id' => $task->getAuthor()->getId()->getValue(),
                'name' => $task->getAuthor()->getName()->getFull(),
            ],
            'date' => $task->getDate()->format(\DATE_ATOM),
            'plan_date' => $task->getPlanDate()?->format(\DATE_ATOM),
            'start_date' => $task->getStartDate()?->format(\DATE_ATOM),
            'end_date' => $task->getEndDate()?->format(\DATE_ATOM),
            'name' => $task->getName(),
            'content' => $processor->process($task->getContent()),
            'files' => array_map(static function (File $file) use ($uploader) {
                return [
                    'id' => $file->getId()->getValue(),
                    'date' => $file->getDate()->format(\DATE_ATOM),
                    'member' => [
                        'id' => $file->getMember()->getId()->getValue(),
                        'name' => $file->getMember()->getName()->getFull(),
                    ],
                    'info' => [
                        'url' => $uploader->generateUrl($file->getInfo()->getPath()),
                        'name' => $file->getInfo()->getName(),
                        'size' => $file->getInfo()->getSize(),
                    ],
                ];
            }, $task->getFiles()),
            'type' => $task->getType()->getName(),
            'progress' => $task->getProgress(),
            'priority' => $task->getPriority(),
            'parent' => $task->getParent() ? [
                'id' => $task->getParent()->getId()->getValue(),
                'name' => $task->getParent()->getName(),
            ] : null,
            'status' => $task->getStatus()->getName(),
            'executors' => array_map(static function (Member $member) {
                return [
                    'id' => $member->getId()->getValue(),
                    'name' => $member->getName()->getFull(),
                ];
            }, $task->getExecutors()),
            'feed' => array_map(static function (Item $item) use ($processor) {
                $action = $item->getAction();
                $comment = $item->getComment();

                return [
                    'date' => $item->getDate()->format(\DATE_ATOM),
                    'action' => $action ? [
                        'id' => $action['id'],
                        'date' => $action['date'],
                        'actor' => [
                            'id' => $action['actor_id'],
                            'name' => $action['actor_name'],
                        ],
                        'set' => [
                            'project' => $action['set_project_id'] ? [
                                'id' => $action['set_project_id'],
                                'name' => $action['set_project_name'],
                            ] : null,
                            'name' => $action['set_name'],
                            'content' => $action['set_content'],
                            'file' => $action['set_file_id'],
                            'removed_file' => $action['set_removed_file_id'],
                            'parent' => $action['set_parent_id'],
                            'removed_parent' => $action['set_removed_parent'],
                            'type' => $action['set_type'],
                            'status' => $action['set_status'],
                            'progress' => $action['set_progress'],
                            'priority' => $action['set_priority'],
                            'plan' => $action['set_plan'],
                            'removed_plan' => $action['set_removed_plan'],
                            'executor' => $action['set_executor_id'] ? [
                                'id' => $action['set_executor_id'],
                                'name' => $action['set_executor_name'],
                            ] : null,
                            'revoked_executor' => $action['set_revoked_executor_id'] ? [
                                'id' => $action['set_revoked_executor_id'],
                                'name' => $action['set_revoked_executor_name'],
                            ] : null,
                        ],
                    ] : null,
                    'comment' => $comment ? [
                        'id' => $comment['id'],
                        'date' => $comment['date'],
                        'author' => [
                            'id' => $comment['author_id'],
                            'name' => $comment['author_name'],
                        ],
                        'content' => $processor->process($comment['text']),
                    ] : [],
                ];
            }, $feed->getItems()),
        ]);
    }
}
