<?php

declare(strict_types=1);

namespace App\Controller;
use App\Entity\User;
use App\Entity\UserInterface;
use App\Repository\UserRepository;
use App\Transfer\UserRegisterJsonTransfer;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth', name: "api_auth")]
class ApiAuthController extends AbstractController
{
    #[Route('/register', name: '_register', methods: ["POST"])]
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
            ], JsonResponse::HTTP_FORBIDDEN);
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
            $user->getPassword()
        );
        $user->setPassword($hashedPassword);

        $repo->upgradePassword($user, $hashedPassword);

        return $this->json([
            'message' => 'success',
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/logout/{deviceType}', name: '_logout', defaults: ['deviceType' => 'web'], methods: ['GET', 'POST', 'OPTIONS'])]
    public function logout(): never {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
