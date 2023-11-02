<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AnalyticsTrackNotification;
use App\Entity\User;
use App\Repository\AnalyticsTrackNotificationRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function is_string;

#[Route('/api/analytics', name: "api_analytics")]
class AnalyticsController extends AbstractController
{
    #[Route("/track/notification", name: "_track_notification", methods: ["POST"])]
    public function track(
        Request $request,
        AnalyticsTrackNotificationRepository $repository,
        UserRepository $userRepository
    ): JsonResponse {
        $user = $userRepository->loadUserByIdentifier($this->getUser()->getUserIdentifier());
        if ($user instanceof User) {
            $message = $request->getContent();
            if (is_string($message)) {
                $entity = new AnalyticsTrackNotification();
                $entity->setUser($user);
                $entity->setMessage(json_decode($message, true));

                $repository->add($entity);
                $repository->save();
            }
        }

        return $this->json(['message' => 'success']);
    }
}
