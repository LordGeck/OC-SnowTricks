<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Form\TrickType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickController extends AbstractController
{
    #[Route('/trick/create', name: 'trick_create')]
    public function create(Request $request, EntityManagerInterface $manager, FileUploader $uploader ): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugger = new AsciiSlugger();
            $trick->setCreatedAt(new \DateTime())
                ->setUser($this->getUser())
                ->setSlug($slugger->slug($form->get('name')->getData()));
            
            foreach($trick->getImages() as $image)
            {
                $image->setTrick($trick);
                $imageFile = $form->get('file')->getData();
                if ($imageFile) {
                    $imageFileName = $uploader->upload($imageFile);
                    $trick->setPath($imageFileName);
                }
                $manager->persist($image);
            }

            foreach($trick->getVideos() as $video)
            {
                $video->setTrick($trick);
                $manager->persist($video);
            }
            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('trick/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/trick/page/{slug}', name: 'trick_page')]
    public function show(string $slug, TrickRepository $repository): Response
    {
        $trick = $repository->findOneBySlug($slug);

        return $this->render('trick/page.html.twig', ['trick' => $trick]);
    }
}
