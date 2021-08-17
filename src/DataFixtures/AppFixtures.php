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

        // demo user
        $user = new User();
        $user->setUsername('DemoUser')
            ->setPassword($this->encoder->encodePassword($user, 'password'))
            ->setEmail('user@demo.com');
        $manager->persist($user);

        // 2 demo category
        $grab = new Category();
        $grab->setName('Grab');
        $rotation = new Category();
        $rotation->setName('Rotation');
        $manager->persist($grab);
        $manager->persist($rotation);
        
        // tricks
        $demoTricks = [
            [
                'Mute',
                'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',
                $grab,
            ],
            [
                'Melancholie',
                'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.',
                $grab,
            ],
            [
                'Indy',
                'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.',
                $grab,
            ],
            [
                'Stalefish',
                'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.',
                $grab,
            ],
            [
                'Tail grab',
                'Saisie de la partie arrière de la planche, avec la main arrière.',
                $grab,
            ],
            [
                'Nose grab',
                'Saisie de la partie avant de la planche, avec la main avant.',
                $grab,
            ],
            [
                'Japan',
                'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.',
                $grab,
            ],
            [
                'Seat belt',
                'Saisie du carre frontside à l\'arrière avec la main avant.',
                $grab,
            ],
            [
                'Truck driver',
                'Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture).',
                $grab,
            ],
            [
                'Trois six',
                '360, trois six pour un tour complet.',
                $rotation,
            ],
            [
                'Cinq quatre',
                '540, cinq quatre pour un tour et demi.',
                $rotation,
            ],
            [
                'Sept deux',
                '720, sept deux pour deux tours complets.',
                $rotation,
            ],
            [
                'Big foot',
                '1080 ou big foot pour trois tours.',
                $rotation,
            ],
        ];

        // demo images
        $demoImages = [
            'demo1.jpg',
            'demo2.jpg',
            'demo3.jpg',
            'demo4.jpg',
            'demo5.jpg',
            'demo6.jpg',
            'demo7.jpg',
        ];

        // generate tricks
        foreach ($demoTricks as $demoTrick) {
            $trick = new Trick();
            $trick->setName($demoTrick[0])
                ->setSlug($slugger->slug($demoTrick[0]))
                ->setDescription($demoTrick[1])
                ->setCreatedAt(new \DateTime)
                ->setUser($user)
                ->setCategory($demoTrick[2]);
            
            // generate featured image
            $featuredImage = new Image();
            $featuredImage->setName($demoTrick[0] . ' image')
                ->setPath($demoImages[array_rand($demoImages)])
                ->setTrick($trick);
            $trick->setFeaturedImage($featuredImage);
            $manager->persist($featuredImage);

            // generate 3 more images
            for ($i = 0; $i < 3; $i++) {
                $image = new Image();
                $image->setName($demoTrick[0] . ' image')
                    ->setPath($demoImages[array_rand($demoImages)])
                    ->setTrick($trick);
                $manager->persist($image);
            }

            $manager->persist($trick);
        }
        $manager->flush();
    }
}
