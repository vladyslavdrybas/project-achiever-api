<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\RouteConstants;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractController extends SymfonyAbstractController
{
    public const LOGIN_ROUTE = RouteConstants::LOGIN_ROUTE;
    public const HOMEPAGE_ROUTE = RouteConstants::HOMEPAGE_ROUTE;

    protected EntityManagerInterface $entityManager;
    protected UrlGeneratorInterface $urlGenerator;
    protected SerializerInterface $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->serializer = $serializer;
    }

    protected function getUser(): ?User
    {
        $user = parent::getUser();

        if (null === $user) {
            return null;
        }

        return $this->entityManager->getRepository(User::class)->loadUserByIdentifier($user->getUserIdentifier());
    }

    protected function getHomepageUrl(): string
    {
        return $this->urlGenerator->generate(self::HOMEPAGE_ROUTE, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    protected function success(): JsonResponse
    {
        return parent::json(
            [
                'message' => 'success'
            ]
        );
    }
}
