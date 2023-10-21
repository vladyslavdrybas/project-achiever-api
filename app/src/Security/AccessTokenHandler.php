<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Token;
use App\Repository\TokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly TokenRepository $repository
    ) {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $token = $this->repository->decode($accessToken);

        if (!$token instanceof Token) {
            throw new BadCredentialsException('Invalid token credentials.');
        }

        // and return a UserBadge object containing the user identifier from the found token
        return new UserBadge($token->getUser()->getUserIdentifier());
    }
}
