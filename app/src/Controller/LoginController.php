<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserInterface;
use App\Repository\TokenRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{

    #[Route('/login/json', name: 'login_json', methods: ["POST"])]
    public function index(): void {
        // App\Security\JsonLoginAuthenticator
    }

    #[Route('/logout/json', name: 'logout_json', methods: ['GET'])]
    public function logout(
        Security $security,
        TokenRepository $tokenRepository
    ): JsonResponse {
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            $tokenRepository->removeAllByUser($user);
            $security->logout(false);
        }

        $data = [
            'message' => 'success',
            'path' => [
                'target' => $this->getHomepageUrl(),
                'login' => $this->getLoginUrl(),
            ],
        ];

        return $this->json($data);
    }
}
