<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Transfer\UserLoginJsonTransfer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Serializer\SerializerInterface;

class JsonLoginAuthenticator extends AbstractAuthenticator
{
    public const LOGIN_ROUTE = 'login_json';
    public const LOGOUT_ROUTE = 'logout_json';
    public const HOMEPAGE_ROUTE = 'app_homepage';

    protected SerializerInterface $serializer;
    protected UrlGeneratorInterface $urlGenerator;
    protected RequestStack $requestStack;
    protected UserRepository $userRepository;
    protected TokenRepository $tokenRepository;

    public function __construct(
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        UserRepository $userRepository,
        TokenRepository $tokenRepository
    ) {
        $this->serializer = $serializer;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
    }

    public function supports(Request $request): ?bool
    {
        return static::LOGIN_ROUTE === $request->attributes->get('_route')
            && !!$request->getContent()
            && $request->getContentTypeFormat() === 'json';
    }

    public function authenticate(Request $request): Passport
    {
        $transfer = $this->serializer->deserialize($request->getContent(), UserLoginJsonTransfer::class, 'json');

        $userBadge = new UserBadge(
            $transfer->getEmail(),
            function($userIdentifier) {
                $user = $this->userRepository->findByEmail($userIdentifier);
                if (!$user instanceof User) {
                    throw new UserNotFoundException();
                }

                return $user;
            }
        );

        $user = $userBadge->getUser();
        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }

        $credentials = new PasswordCredentials($transfer->getPassword() . $user->getRawId());

        return new Passport($userBadge, $credentials);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param string $firewallName
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->urlGenerator->generate(static::HOMEPAGE_ROUTE, [], UrlGeneratorInterface::ABSOLUTE_URL);
        $logoutPath = $this->urlGenerator->generate(static::LOGOUT_ROUTE, [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var User $user */
        $user = $token->getUser();

        $token = $this->tokenRepository->generateForUser($user);
        $this->tokenRepository->add($token);
        $this->tokenRepository->save();

        $tokenEncoded = $this->tokenRepository->encode($token);

        $data = [
            'message' => 'success',
            'user' => [
                'id' => $user->getRawId(),
            ],
            'path' => [
                'target' => $targetPath,
                'logout' => $logoutPath,
            ],
            'accessToken' => [
                'id' => $tokenEncoded,
                'expiredAt' => $token->getExpireAt()->getTimestamp(),
            ],
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
            'path' => [
                'target' => $this->getLoginUrl(),
            ],
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
