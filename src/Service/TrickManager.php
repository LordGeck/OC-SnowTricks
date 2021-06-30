<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploader $uploader,
        private SluggerInterface $slugger,
    ) {}

    public function persist(Trick $trick, User $user): void
    {
        $trick->setCreatedAt(new \DateTime());
        if (!$trick->getUser()) {
            $trick->setUser($user);
        }
        $trick->setSlug($this->slugger->slug($trick->getName()));

        foreach ($trick->getImages() as $image) {
            $image->setTrick($trick);
            if ($image->getFile()) {
                $image->setPath($this->uploader->upload($image->getFile()));
            }
            $this->entityManager->persist($image);
        }

        foreach ($trick->getVideos() as $video) {
            $video->setTrick($trick);
            $this->entityManager->persist($video);
        }

        $this->entityManager->persist($trick);
        $this->entityManager->flush();
    }

    public function delete(Trick $trick): void
    {
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
    }
}
