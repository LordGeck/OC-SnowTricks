<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CommentManager
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function persist(Comment $comment, Trick $trick, User $user): void
    {
        $comment->setCreatedAt(new \DateTime());
        $comment->setTrick($trick);
        $comment->setUser($user);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    public function delete(Comment $comment): void
    {
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
    }
}
