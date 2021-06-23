<?php

namespace App\Service;

use App\Entity\Trick;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickManager
{
    private $manager;
    private $security;
    private $uploader;
    private $slugger;

    public function __construct(
        EntityManagerInterface $manager,
        Security $security,
        FileUploader $uploader,
        SluggerInterface $slugger
    ) {
        $this->manager = $manager;
        $this->security = $security;
        $this->uploader = $uploader;
        $this->slugger = $slugger;
    }

    public function persist(Trick $trick)
    {
        $trick->setCreatedAt(new \DateTime());
        if (!$trick->getUser()) {
            $trick->setUser($this->security->getUser());
        }
        $trick->setSlug($this->slugger->slug($trick->getName()));

        foreach ($trick->getImages() as $image) {
            $image->setTrick($trick);
            if ($image->getFile()) {
                $image->setPath($this->uploader->upload($image->getFile()));
            }
            $this->manager->persist($image);
        }

        foreach ($trick->getVideos() as $video) {
            $video->setTrick($trick);
            $this->manager->persist($video);
        }

        $this->manager->persist($trick);
        $this->manager->flush();
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
