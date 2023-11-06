<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\FcmTokenDeviceType;
use App\Entity\FirebaseCloudMessaging;
use App\Repository\FirebaseCloudMessagingRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use function base64_decode;

// TODO remove credentials on frontend -> use server token to register fcm token
#[Route('/firebase', name: "api_firebase")]
class FirebaseCloudMessagingController extends AbstractController
{
    #[Route("/store/token/{token}/{deviceType}", name: "_store_token", methods: ["GET", "OPTIONS", "HEAD"])]
    public function store(
        string $token,
        string $deviceType,
        FirebaseCloudMessagingRepository $repository,
        UserRepository $userRepository
    ): JsonResponse {
        try {
            $user = $userRepository->findByEmail($this->getUser()->getUserIdentifier());
            if (!$user->isActive()) {
                throw new \Exception('User is not active.');
            }

            $tokenNew = new FirebaseCloudMessaging();
            $tokenNew->setToken(base64_decode($token));
            $tokenNew->setDeviceType($deviceType);
            $tokenNew->setUser($user);
            $tokenNew->prolong();

            $tokens = $repository->findBy([
                'deviceType' => $tokenNew->getDeviceType(),
                'user' => $tokenNew->getUser(),
            ]);

            if (count($tokens)) {
                foreach ($tokens as $t) {
                    if ($t->getToken() === $tokenNew->getToken()) {
                        if (null == $t->getExpireAt()) {
                            throw new \Exception('Token not found on FCM.');
                        }

                        $t->prolong();
                        $tokenNew = $t;

                        break;
                    } else {
                        $repository->remove($t);
                    }
                }
            }

            $repository->add($tokenNew);
            $repository->save();
        } catch (Exception $e) {
            return $this->json(
                [
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $data = $this->serializer->normalize($tokenNew);

        return $this->json($data);
    }

    #[Route("/prolong/token/{deviceType}", name: "_prolong_token", methods: ["GET", "OPTIONS", "HEAD"])]
    public function prolong(
        string $deviceType,
        FirebaseCloudMessagingRepository $repository,
        UserRepository $userRepository
    ): JsonResponse {
        try {
            $user = $userRepository->findByEmail($this->getUser()->getUserIdentifier());
            if (!$user->isActive()) {
                throw new \Exception('User is not active.');
            }

            $token = $repository->findOneBy([
                'deviceType' => FcmTokenDeviceType::getOrDefault($deviceType),
                'user' => $user,
            ]);

            if (!$token instanceof FirebaseCloudMessaging) {
                throw new \Exception('No tokens found');
            }

            $token->prolong();

            $repository->add($token);
            $repository->save();
        } catch (Exception $e) {
            return $this->json(
                [
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $data = $this->serializer->normalize($token);

        return $this->json($data);
    }
}
