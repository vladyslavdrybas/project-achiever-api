<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile', name: "api_profile")]
class ProfileController extends AbstractController
{
    #[Route("/", name: "", methods: ["GET"])]
    public function profile(): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $data = $this->serializer->normalize($user);

        return $this->json($data);
    }
}
