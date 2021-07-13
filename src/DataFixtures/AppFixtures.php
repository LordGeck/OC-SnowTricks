<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $slugger = new AsciiSlugger();
        $testImages = ['img1','img2','img3'];
        $testVideos = ['video1','video2','video3'];
        //test user
        $user = new User();
        $user->setUsername('Test')
            ->setPassword($this->encoder->encodePassword($user, 'password'))
            ->setEmail('test@test.com');
        $manager->persist($user);

        // generate 3 category
        for ($i = 0; $i < 3; $i++) {
            $category = new Category();
            $name = 'Catégorie ' . $i;
            $category->setName($name);
            $manager->persist($category);
        }
        
        // generate 25 tricks
        for ($i = 0; $i < 25; $i++) {
            $trick = new Trick();
            $name = 'Trick ' . $i;
            $slug = $slugger->slug($name);
            $trick->setName($name)
                ->setSlug($slug)
                ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam at varius massa.')
                ->setCreatedAt(new \DateTime)
                ->setUser($user)
                ->setCategory($category);
            // add demo images
            foreach ($testImages as $images)
            {
                $image = new Image();
                $image->setName($images)
                    ->setPath('https://via.placeholder.com/600x300')
                    ->setTrick($trick);
                $manager->persist($image);
            }
            // add demo video
            foreach ($testVideos as $videos)
            {
                $video = new Video();
                $video->setUrl($videos . '.com')
                    ->setTrick($trick);
                $manager->persist($video);
            }
            $trick->setFeaturedImage($image);
            $manager->persist($trick);
        }

        $manager->flush();
    }
}
