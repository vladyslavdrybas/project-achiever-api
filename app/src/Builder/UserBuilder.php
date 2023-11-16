<?php

declare(strict_types=1);

namespace App\Builder;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserBuilder implements IEntityBuilder
{
    public function __construct(
        protected readonly UserPasswordHasherInterface $passwordHasher,
        protected readonly UserRepository $userRepository
    ) {}

    public function baseUser(
        string $email,
        string $password,
        ?string $username = null
    ): User{
        $user = new User();

        $user->setEmail($email);
        $user->setPassword($password);

        if (null === $username) {
            $user->setRandomUsername();
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );

        $user->setPassword($hashedPassword);

        return $user;
    }
}
