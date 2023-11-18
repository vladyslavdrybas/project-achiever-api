<?php

declare(strict_types=1);

namespace App\Event\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JWTCreatedListener
{
    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly TokenStorageInterface $token
    ) {}

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $this->token->getToken()->getUser();

        $payload = $event->getData();
        $payload['username'] = $user->getUsername();
        $payload['id'] = $user->getRawId();
        unset($payload['email']);
        unset($payload['roles']);

        // TODO add usergroup/list/achievement roles

        $event->setData($payload);
    }
}
