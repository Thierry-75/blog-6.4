<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function encodePassword(User $user): void
    {
        if($user->getPlainPassword()=== null)return;

        $user->setPassword($this->hasher->hashPassword($user,$user->getPlainPassword()));
        $user->setPlainPassword(null);
    }

    public function prePersist(User $user): void
    {
        $this->encodePassword($user);
    }
    public function preUpdate(User $user): void
    {
        $this->encodePassword($user);
    }
}