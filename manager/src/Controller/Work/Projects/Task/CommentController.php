<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Task;

use App\Controller\ErrorHandler;
use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\Entity\Comment\CommentRepository;
use App\Model\Comment\Entity\Comment\Id;
use App\Model\Comment\UseCase\Comment\Create as CommentCreate;
use App\Model\Comment\UseCase\Comment\Edit;
use App\Model\Comment\UseCase\Comment\Remove;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Security\Voter\Comment\CommentAccess;
use App\Security\Voter\Work\Projects\TaskAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly ErrorHandler $errors,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/work/projects/tasks/{id}/comments', name: 'api.work.projects.tasks.comments.add', methods: ['POST'])]
    public function addComment(
        Task $task,
        Request $request,
        CommentCreate\Handler $handler,
        ValidatorInterface $validator,
    ): JsonResponse {
        $this->denyAccessUnlessGranted(TaskAccess::VIEW, $task);

        $data = json_decode($request->getContent(), true);

        $command = new CommentCreate\Command(
            $this->getUser()->getId(),
            Task::class,
            (string) $task->getId()->getValue()
        );
        $command->text = $data['text'] ?? '';

        $violations = $validator->validate($command);
        if (\count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], 422);
        }

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);

            return new JsonResponse(['message' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'ok'], 201);
    }

    #[Route('/work/projects/tasks/{id}/comments/{comment_id}/edit', name: 'work.projects.tasks.comments.edit', methods: ['PUT'])]
    public function edit(
        Task $task,
        string $comment_id,
        CommentRepository $commentRepository,
        Request $request,
        Edit\Handler $handler,
    ): JsonResponse {
        $comment = $commentRepository->get(new Id($comment_id));
        $this->denyAccessUnlessGranted(TaskAccess::VIEW, $task);
        $this->checkCommentIsForTask($task, $comment);
        $this->denyAccessUnlessGranted(CommentAccess::MANAGE, $comment);

        $command = Edit\Command::fromComment($comment);
        $data = json_decode($request->getContent(), true);

        $command->text = $data['text'] ?? '';

        $violations = $this->validator->validate($command);
        if (\count($violations) > 0) {
            return $this->json(['errors' => (string) $violations], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $handler->handle($command);

            return $this->json(['status' => 'ok']);
        } catch (\DomainException $e) {
            $this->errors->handle($e);

            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/work/projects/tasks/{id}/comments/{comment_id}/delete', name: 'work.projects.tasks.comments.delete', methods: ['post'])]
    public function delete(
        Task $task,
        string $comment_id,
        CommentRepository $commentRepository,
        Remove\Handler $handler,
    ): JsonResponse {
        $comment = $commentRepository->get(new Id($comment_id));
        $this->denyAccessUnlessGranted(TaskAccess::VIEW, $task);
        $this->checkCommentIsForTask($task, $comment);
        $this->denyAccessUnlessGranted(CommentAccess::MANAGE, $comment);

        $command = new Remove\Command($comment->getId()->getValue());

        try {
            $handler->handle($command);

            return $this->json(['status' => 'ok']);
        } catch (\DomainException $e) {
            $this->errors->handle($e);

            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function checkCommentIsForTask(Task $task, Comment $comment): void
    {
        if (!(
            $comment->getEntity()->getType() === Task::class
            && (int) $comment->getEntity()->getId() === $task->getId()->getValue()
        )) {
            throw $this->createNotFoundException();
        }
    }
}
