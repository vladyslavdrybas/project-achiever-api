<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/login', name: "login")]
class LoginController extends AbstractController
{

    #[Route('/json', name: '_json', methods: ["POST"])]
    public function index(): void {
        // App\Security\JsonLoginAuthenticator
    }

    #[Route('/logout/json', name: '_logout_json', methods: ['GET'])]
    public function logout(
        Security $security
    ): JsonResponse {
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
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
