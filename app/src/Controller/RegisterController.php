<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserInterface;
use App\Repository\UserRepository;
use App\Transfer\UserRegisterJsonTransfer;
use Symfony\Bundle\SecurityBundle\Security;
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
        UserRepository $repo,
        Security $security
    ): JsonResponse {
        $exist = $repo->findByEmail($userRegisterJsonTransfer->getEmail());

        if ($exist instanceof User) {
            return $this->json([
                'message' => 'such a user already exists.'
            ], JsonResponse::HTTP_PRECONDITION_FAILED);
        }

        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            $security->logout(false);
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

        $data = $this->serializer->normalize($user);

        return $this->json([
            'message' => 'success',
            'user' => $data,
            'path' => [
                'target' => $this->getHomepageUrl(),
                'login' => $this->getLoginUrl(),
            ],
        ], JsonResponse::HTTP_OK);
    }
}
