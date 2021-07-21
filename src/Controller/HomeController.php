<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function home(TrickRepository $repository): Response
    {
        $tricks = $repository->findBy([], ['createdAt' => 'ASC'], 8, 0);

        return $this->render('home.html.twig', ['tricks' => $tricks]);
    }

    #[Route('/home/moreTricks', name: 'load_more_tricks')]
    public function loadMore(TrickRepository $repository, Request $request): Response
    {
        $tricks = $repository->findBy(
            [],
            ['createdAt' => 'ASC'],
            $request->query->get('itemLimit'),
            $request->query->get('itemOffset'),
        );

        return new JsonResponse(
            ['html' => $this->render('trick/_list.html.twig', ['tricks' => $tricks])->getContent()]
        );
    }
}
