<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Form\CommentType;
use App\Service\CommentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(private CommentManager $commentManager) {}

    #[Route('/comment/delete/{id}', name: 'comment_delete')]
    public function delete(string $id, CommentRepository $repository): Response
    {
        $comment = $repository->findOneById($id);
        if ($comment->getUser() === $this->getUser()) {
            $this->commentManager->delete($comment);
        }
        return $this->redirectToRoute('trick_page', ['slug' => $comment->getTrick()->getSlug(),
        '_fragment' => 'comments'
        ]);
    }
}
