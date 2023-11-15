<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\ShareObjectToken;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Symfony\Component\HttpFoundation\Request;

class JWTTokenAuthenticator extends JWTAuthenticator
{
    public function supports(Request $request): ?bool
    {
        if (
            $request->attributes->get('_route') === 'api_achievement_list_achievement_show'
            && null !== $request->query->get(ShareObjectToken::QUERY_IDENTIFIER)
        ) {
            return false;
        }

        return false !== $this->getTokenExtractor()->extract($request);
    }
}
