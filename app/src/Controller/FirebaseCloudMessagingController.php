<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\FirebaseCloudMessaging;
use App\Repository\FirebaseCloudMessagingRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use function array_map;
use function base64_decode;

#[Route('/api/firebase', name: "api_firebase")]
class FirebaseCloudMessagingController extends AbstractController
{
    // TODO remove credentials on frontend -> use server token to register fcm token
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
            $entity->setExpireAt((new DateTime('+5 minutes')));

            $tokens = $repository->findBy([
                'token' => $entity->getToken(),
                'deviceType' => $entity->getDeviceType(),
                'user' => $entity->getUser(),
            ]);

            if (!count($tokens)) {
                $tokens = $repository->findBy([
                    'deviceType' => $entity->getDeviceType(),
                    'user' => $entity->getUser(),
                ]);

                array_map(
                    function (FirebaseCloudMessaging $fcm) use ($repository) {
                        $repository->remove($fcm);
                    },
                    $tokens
                );

                $repository->add($entity);
                $repository->save();
            }
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
