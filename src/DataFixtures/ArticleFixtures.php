<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ArticleFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher, private SluggerInterface $slugger)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');


        for($j=0; $j < 10; $j++){
        $autor = new User();
        $autor->setEmail($faker->email())
            ->setRoles(['ROLE_REDACTOR'])
            ->setCreatedAt(new \DateTimeImmutable())
            ->setPseudo(mt_rand(0, 1) === 1 ? $faker->firstNameFemale() . '#' . mt_rand(10, 90) : $faker->firstNameMale() . '#' . mt_rand(10, 90))
            ->setPassword($this->userPasswordHasher->hashPassword($autor, 'ArethiA75!'))
            ->setIsVerified(mt_rand(0, 1 === 1 ? true : false));
        if ($autor->IsVerified(true)) {
            $autor->setIsNewsLetter(true)
                ->setIsFull(false);
        }
        for ($i = 0; $i < 10; $i++) {
            $article = new Article();
            $article->setUser($autor)
                ->setTitle($faker->sentence(3))
                ->setSlug(strtolower($faker->slug()))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setState(mt_rand(0,1)===1 ? Article::STATES[0]:Article::STATES[1])
                ->setContent($faker->paragraph())
                ->setImage('default.webp');
            $manager->persist($article);
        }
        $manager->persist($autor);
    }
        $manager->flush();
    }
    
}
