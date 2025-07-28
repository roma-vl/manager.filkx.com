<?php

declare(strict_types=1);

namespace App\Event\Listener\Work\Projects\Task;

use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Task\Event\TaskExecutorAssigned;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

readonly class EmailNotificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TaskRepository   $tasks,
        private MemberRepository $members,
        private MailerInterface  $mailer,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            TaskExecutorAssigned::class => [
                ['onTaskExecutorAssignedExecutor'],
                ['onTaskExecutorAssignedAuthor'],
            ],
        ];
    }

    public function onTaskExecutorAssignedExecutor(TaskExecutorAssigned $event): void
    {
        if ($event->executorId->isEqual($event->actorId)) {
            return;
        }

        $task = $this->tasks->get($event->taskId);
        $executor = $this->members->get($event->executorId);
        $author = $task->getAuthor();

        if ($executor === $author) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@yourapp.com', 'Task Manager'))
            ->to(new Address($executor->getEmail()->getValue(), $executor->getName()->getFull()))
            ->subject('Task Executor Assignment')
            ->htmlTemplate('mail/work/projects/task/executor-assigned-executor.html.twig')
            ->context([
                'task' => $task,
                'executor' => $executor,
            ]);

        $this->mailer->send($email);
    }

    public function onTaskExecutorAssignedAuthor(TaskExecutorAssigned $event): void
    {
        $task = $this->tasks->get($event->taskId);
        $executor = $this->members->get($event->executorId);
        $author = $task->getAuthor();

        if ($executor === $author) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@yourapp.com', 'Task Manager'))
            ->to(new Address($author->getEmail()->getValue(), $author->getName()->getFull()))
            ->subject('Your Task Executor Assignment')
            ->htmlTemplate('mail/work/projects/task/executor-assigned-author.html.twig')
            ->context([
                'task' => $task,
                'author' => $author,
                'executor' => $executor,
            ]);

        $this->mailer->send($email);
    }
}
