<?php

declare(strict_types=1);

namespace App\ReadModel\Comment;

class CommentRow
{
    public string $id;
    public \DateTimeImmutable $date;
    public int $author_id;
    public string $author_name;
    public string $email;
    public string $text;

    public function __construct(string $id, string $text, \DateTimeImmutable $date, string $author_name, string $author_email)
    {
        $this->id = $id;
        $this->text = $text;
        $this->date = $date;
        $this->author_name = $author_name;
        $this->email = $author_email;
    }
}
