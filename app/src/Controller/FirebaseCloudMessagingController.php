<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\FirebaseCloudMessaging;
use App\Repository\FirebaseCloudMessagingRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use function base64_decode;

#[Route('/api/firebase', name: "api_firebase")]
class FirebaseCloudMessagingController extends AbstractController
{
    #[Route("/store/token/{token}/{deviceType}", name: "_store_token", methods: ["GET", "OPTIONS", "HEAD"])]
    public function index(
        string $token,
        string $deviceType,
        FirebaseCloudMessagingRepository $repository,
        UserRepository $userRepository
    ): JsonResponse {
        try {
            $user = $userRepository->findByEmail($this->getUser()->getUserIdentifier());
            $entity = new FirebaseCloudMessaging();
            $entity->setToken(base64_decode($token));
            $entity->setDeviceType($deviceType);
            $entity->setUser($user);

            $repository->add($entity);
            $repository->save();
        } catch (Exception $e) {
            return $this->json(
                [
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->json(["message" => "success"]);
    }
}