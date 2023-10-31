<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\FcmTokenDeviceType;
use App\Repository\FirebaseCloudMessagingRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected readonly FirebaseCloudMessagingRepository $messagingRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {
        // get the security token of the session that is about to be logged out
        $token = $event->getToken();

        // get the current request
        $request = $event->getRequest();
        $deviceType = $request->attributes->get('deviceType') ?? '';

        // get the current response, if it is already set by another listener
//        $response = $event->getResponse();

        $fcmTokens = $this->messagingRepository->findBy([
            'deviceType' => FcmTokenDeviceType::getOrDefault($deviceType),
            'user' => $token->getUser(),
        ]);

        if (count($fcmTokens)) {
            foreach ($fcmTokens as $t) {
                $this->messagingRepository->remove($t);
            }
            $this->messagingRepository->save();
        }

        // configure a custom logout response
        $response = new JsonResponse(
            [
                'message' => 'success',
            ],
            JsonResponse::HTTP_OK
        );

        $event->setResponse($response);
    }
}
