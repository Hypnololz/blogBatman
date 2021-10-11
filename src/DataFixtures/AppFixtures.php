<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');
        //creation compte admin
        $admin = new User();

        //hydrate

        $admin
            ->setEmail('a@a.a')
            ->setCreationDate( $faker->dateTimeBetween('-1 year','now'))
            ->setPseudo('enzoadmin')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword(
                $this->encoder->hashPassword($admin, ' ')
            )
        ;
        $manager->persist($admin);

        for ($i = 0; $i < 50; $i++){
            $user = new User();
            $user
                ->setEmail($faker->email)
                ->setCreationDate( $faker->dateTimeBetween('-1 year','now'))
                ->setPseudo($faker->userName)
                ->setPassword(
                    $this->encoder->hashPassword($user, 'a'))
            ;
            $manager->persist($user);
        }
        for ($i = 0; $i < 2000; $i++){
            $article = new Article();
            $article
                ->setTitle($faker->sentence('10'))
                ->setPublicationDate( $faker->dateTimeBetween('-1 year','now'))
                ->setContent($faker->paragraph(15))
                ->setUser($admin)
            ;
            $manager->persist($article);
        }

        $manager->flush();
    }
}
