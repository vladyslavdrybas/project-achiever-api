<?php

declare(strict_types=1);

namespace App\Builder;

use App\Entity\User;
use App\Exceptions\AlreadyExists;
use App\Repository\UserRepository;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function strlen;

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
        if (strlen($email) < 6) {
            throw new InvalidArgumentException('Invalid email length. Expect string length greater than 5.');
        }

        $exist = $this->userRepository->findByEmail($email);
        if ($exist instanceof User) {
            throw new AlreadyExists('Such a user already exists.');
        }

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
