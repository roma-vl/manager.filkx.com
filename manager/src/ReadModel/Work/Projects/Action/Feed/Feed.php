<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Action\Feed;

use App\Service\Work\Processor\Processor;

class Feed
{
    private array $actions;
    private array $comments;
    private Processor $processor;

    public function __construct(array $actions, array $comments, Processor $processor)
    {
        $this->actions = $actions;
        $this->comments = $comments;
        $this->processor = $processor;
    }

    public function getItems(): array
    {
        $items = [];

        foreach ($this->actions as $action) {
            $date = $action['date'] instanceof \DateTimeImmutable ? $action['date'] : new \DateTimeImmutable($action['date']);
            $items[] = Item::forAction($date, $action);
        }

        foreach ($this->comments as $comment) {
            $date = $comment->date instanceof \DateTimeImmutable ? $comment->date : new \DateTimeImmutable($comment->date);

            $comment->text_raw = $comment->text;
            $comment->text = $this->processor->process($comment->text);

            $items[] = Item::forComment($date, $comment);
        }

        usort($items, static fn (Item $a, Item $b) => $b->getDate() <=> $a->getDate());

        return $items;
    }
}
