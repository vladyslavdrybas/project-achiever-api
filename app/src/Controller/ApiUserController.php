<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: "api")]
class ApiUserController extends AbstractController
{
    #[Route("/user/profile", name: "_user_profile", methods: ["GET", "OPTIONS", "HEAD"])]
    public function profile(): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $data = $this->serializer->normalize($user);

        return $this->json([
            'message' => 'success',
            'user' => $data,
        ]);
    }
}
