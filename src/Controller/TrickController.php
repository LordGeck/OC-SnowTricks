<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Form\TrickType;
use App\Service\TrickManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            $this->trickManager->persist($trick, $this->getUser());

            return $this->redirectToRoute('home');
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

        if ($form->isSubmitted() && $form->isValid()) {
            $this->trickManager->persist($trick, $this->getUser());

            return $this->redirectToRoute('home', ['_fragment' => 'trick-list']);
        }

        if (!$trick->getUser() === $this->getUser()) {
            return $this->redirectToRoute('home', ['_fragment' => 'trick-list']);
        }
        else {
            return $this->render('trick/edit.html.twig', [
                'form' => $form->createView(),
                'trick' => $trick
            ]);
        }
    }

    #[Route('/trick/delete/{slug}', name: 'trick_delete')]
    public function delete(string $slug, TrickRepository $repository): Response
    {
        $trick = $repository->findOneBySlug($slug);

        if ($trick->getUser() === $this->getUser()) {
            $this->trickManager->delete($trick);
        }

        return $this->redirectToRoute('home', ['_fragment' => 'trick-list']);
    }

    #[Route('/trick/page/{slug}', name: 'trick_page')]
    public function show(string $slug, TrickRepository $repository): Response
    {
        $trick = $repository->findOneBySlug($slug);

        return $this->render('trick/page.html.twig', ['trick' => $trick]);
    }
}
