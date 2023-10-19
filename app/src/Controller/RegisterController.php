<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Transfer\UserRegisterJsonTransfer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/register', name: "register")]
class RegisterController extends AbstractController
{
    #[Route('/json', name: '_json', methods: ["POST"])]
    public function index(
        UserPasswordHasherInterface $passwordHasher,
        UserRegisterJsonTransfer $userRegisterJsonTransfer,
        UserRepository $repo
    ): JsonResponse {
        $exist = $repo->findByEmail($userRegisterJsonTransfer->getEmail());

        if ($exist instanceof User) {
            return $this->json([
                'message' => 'such a user already exists.'
            ], JsonResponse::HTTP_PRECONDITION_FAILED);
        }

        $user = new User();
        $user->setEmail($userRegisterJsonTransfer->getEmail());
        $user->setPassword($userRegisterJsonTransfer->getPassword());

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword() . $user->getRawId()
        );
        $user->setPassword($hashedPassword);

        $repo->upgradePassword($user, $hashedPassword);

        return $this->json([
            'message' => 'success',
            'user' => [
                'id' => $user->getRawId(),
                'email' => $user->getEmail(),
            ],
            'path' => [
                'target' => $this->getHomepageUrl(),
                'login' => $this->getLoginUrl(),
            ],
        ], JsonResponse::HTTP_OK);
    }
}
