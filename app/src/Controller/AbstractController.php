<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class AbstractController extends SymfonyAbstractController
{
    public const LOGIN_ROUTE = 'login_json';
    public const LOGOUT_ROUTE = 'login_logout_json';
    public const HOMEPAGE_ROUTE = 'app_homepage';

    protected EntityManagerInterface $entityManager;
    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    protected function getHomepageUrl(): string
    {
        return $this->urlGenerator->generate(self::HOMEPAGE_ROUTE, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
