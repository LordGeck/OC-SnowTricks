<?php
// src/Controller/HomeController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController
{
    /**
     * @Route("/home", name="home")
     */
    public function home(): Response
    {
        return new Response(
            '<html><body>Accueil</body></html>'
        );
    }
}