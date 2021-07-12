<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function home(TrickRepository $repository): Response
    {
        $tricks = $repository->findAll();

        return $this->render('home.html.twig', ['tricks' => $tricks]);
    }
}
