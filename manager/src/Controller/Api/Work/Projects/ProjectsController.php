<?php

declare(strict_types=1);

namespace App\Controller\Api\Work\Projects;

use App\Controller\Api\PaginationSerializer;
use App\ReadModel\Work\Projects\Project\Filter;
use App\ReadModel\Work\Projects\Project\ProjectFetcher;
use Doctrine\DBAL\Exception;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[OA\Get(
    path: '/api/work/projects',
    summary: 'Get list of projects',
    security: [['oauth2' => ['common']]],
    tags: ['Work Projects'],
    parameters: [
        new OA\Parameter(
            name: 'filter[name]',
            description: 'Filter by project name',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'string')
        ),
        new OA\Parameter(
            name: 'filter[status]',
            description: 'Filter by status',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'string')
        ),
        new OA\Parameter(
            name: 'page',
            in: 'query',
            schema: new OA\Schema(type: 'integer', default: 1)
        ),
        new OA\Parameter(
            name: 'sort',
            in: 'query',
            schema: new OA\Schema(type: 'string', default: 'sort')
        ),
        new OA\Parameter(
            name: 'direction',
            in: 'query',
            schema: new OA\Schema(type: 'string', default: 'asc', enum: ['asc', 'desc'])
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Success response',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id', type: 'string', example: 'project-uuid'),
                                new OA\Property(property: 'name', type: 'string', example: 'My Project'),
                                new OA\Property(property: 'status', type: 'string', example: 'active'),
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
#[Route('/api/work/projects', name: 'api.work.projects', methods: ['GET'])]
class ProjectsController extends AbstractController
{
    private const PER_PAGE = 50;

    public function __construct(
        private DenormalizerInterface $denormalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface|Exception
     */
    public function __invoke(Request $request, ProjectFetcher $fetcher): Response
    {
        $filter = $this->isGranted('ROLE_WORK_MANAGE_PROJECTS')
            ? Filter\Filter::all()
            : Filter\Filter::forMember($this->getUser()->getId());

        $filter = $this->denormalizer->denormalize(
            $request->query->all('filter'),
            Filter\Filter::class,
            'array',
            [
                'object_to_populate' => $filter,
                'ignored_attributes' => ['member'],
            ]
        );

        $pagination = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'sort'),
            $request->query->get('direction', 'asc')
        );

        return $this->json([
            'items' => array_map(static fn (array $item) => [
                'id' => $item['id'],
                'name' => $item['name'],
                'status' => $item['status'],
            ], (array) $pagination->getItems()),
            'pagination' => PaginationSerializer::toArray($pagination),
        ]);
    }
}
