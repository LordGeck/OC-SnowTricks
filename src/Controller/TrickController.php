<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\TrickManager;
use App\Service\CommentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    public function __construct(private TrickManager $trickManager) {}

    #[Route('/trick/create', name: 'trick_create')]
    public function create(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick, ['trick' => $trick]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->trickManager->create($trick, $this->getUser());
            $this->addFlash('success', 'Le trick a été créé avec succès');

            return $this->redirectToRoute('home', ['_fragment' => 'trick-list']);
        }

        return $this->render('trick/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/trick/edit/{slug}', name: 'trick_edit')]
    public function edit(string $slug, Request $request, TrickRepository $repository): Response
    {
        $trick = $repository->findOneBySlug($slug);
        $form = $this->createForm(TrickType::class, $trick, ['trick' => $trick]);
        $form->handleRequest($request);

        if (!$trick->getUser() === $this->getUser()) {
            return $this->redirectToRoute('home', ['_fragment' => 'trick-list']);
        } if ($form->isSubmitted() && $form->isValid()) {
            $this->trickManager->edit($trick);
            $slug = $trick->getSlug();
            $this->addFlash('success', 'Le trick a été édité avec succès');

            return $this->redirectToRoute('trick_page', ['slug' => $slug]);
        }

        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }

    #[Route('/trick/delete/{slug}', name: 'trick_delete')]
    public function delete(string $slug, TrickRepository $repository): Response
    {
        $trick = $repository->findOneBySlug($slug);

        if ($trick->getUser() === $this->getUser()) {
            $this->trickManager->delete($trick);
            $this->addFlash('success', 'Le trick a été supprimé avec succès');
        }

        return $this->redirectToRoute('home', ['_fragment' => 'trick-list']);
    }

    #[Route('/trick/page/{slug}', name: 'trick_page')]
    public function show(
        string $slug,
        Request $request,
        CommentManager $commentManager,
        TrickRepository $trickRepository,
        CommentRepository $commentRepository,
    ): Response {
        $trick = $trickRepository->findOneBySlug($slug);
        $comments = $commentRepository->findBy(['trick' => $trick], ['createdAt' => 'DESC'], 5, 0);
        $newComment = new Comment();
        $form = $this->createForm(CommentType::class, $newComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentManager->persist($newComment, $trick, $this->getUser());
            
            return $this->redirectToRoute('trick_page', ['slug' => $slug, '_fragment' => 'comments']);
        }

        return $this->render('trick/page.html.twig', ['trick' => $trick, 'comments' => $comments,
            'form' => $form->createView()
        ]);
    }

    #[Route('/trick/page/{slug}/moreComments', name: 'load_more_comments')]
    public function loadMore(
        string $slug,
        Request $request,
        TrickRepository $trickRepository,
        CommentRepository $commentRepository,
    ): Response {
        $trick = $trickRepository->findOneBySlug($slug);
        $comments = $commentRepository->findBy(
            ['trick' => $trick],
            ['createdAt' => 'DESC'],
            $request->query->get('itemLimit'),
            $request->query->get('itemOffset'),
        );

        return new JsonResponse(
            ['html' => $this->render('trick/_comments.html.twig', ['comments' => $comments])->getContent()]
        );
    }
}
