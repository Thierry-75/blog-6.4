<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }


    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i=0; $i<20; $i++){
                            $user = new User();
        $user->setEmail($faker->email())
            ->setRoles(['ROLE_USER'])
            ->setCreatedAt(new \DateTimeImmutable())
            ->setPseudo(mt_rand(0, 1) === 1 ? $faker->firstNameFemale() . '#' . mt_rand(10, 90) : $faker->firstNameMale() . '#' . mt_rand(10, 90))
            ->setPassword($this->userPasswordHasher->hashPassword($user, 'ArethiA75!'))
            ->setIsVerified(mt_rand(0, 1 === 1 ? true : false));
        if ($user->IsVerified(true)) {
            $user->setIsNewsLetter(true)
                ->setIsFull(false);
        }
        $manager->persist($user);
        }
        
        $manager->flush();
    }
}
