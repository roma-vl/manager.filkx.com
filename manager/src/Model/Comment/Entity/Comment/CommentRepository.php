<?php

declare(strict_types=1);

namespace App\Model\Comment\Entity\Comment;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CommentRepository
{
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Comment::class);
        $this->em = $em;
    }

    public function get(Id $id): Comment
    {
        /** @var Comment $comment */
        if (!$comment = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Comment is not found.');
        }

        return $comment;
    }

    public function add(Comment $comment): void
    {
        $this->em->persist($comment);
    }

    public function remove(Comment $comment): void
    {
        $this->em->remove($comment);
    }
}
