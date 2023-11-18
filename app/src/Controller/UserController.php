<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: "api_user")]
class UserController extends AbstractController
{
    #[Route("/{user}", name: "_show", methods: ["GET"])]
    public function profile(
        User $user
    ): JsonResponse {
        /** @var \App\Entity\User $user */
        $owner = $this->getUser();
        if ($user === $owner) {
            $data = $this->serializer->normalize($user);
        } else {
            $cleanedUser = new User();
            $cleanedUser->setId($user->getId());
            $cleanedUser->setEmail('');
            $cleanedUser->setPassword('');
            $cleanedUser->setLocale($user->getLocale());
            $cleanedUser->setUsername($user->getUsername());
            $cleanedUser->setIsActive($user->isActive());
            $cleanedUser->setIsBanned($user->isBanned());
            $cleanedUser->setIsDeleted($user->isDeleted());
            $cleanedUser->setIsEmailVerified($user->isEmailVerified());

            $data = $this->serializer->normalize($cleanedUser);
        }

        return $this->json($data);
    }
}
