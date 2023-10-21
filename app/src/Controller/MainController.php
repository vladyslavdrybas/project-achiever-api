<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: "app")]
class MainController extends AbstractController
{
    #[Route("/", name: "_homepage", methods: ["GET", "OPTIONS", "HEAD"])]
    public function index(): JsonResponse
    {
        $repo = $this->entityManager->getRepository(Tag::class)->findAll();

        return $this->json($repo);
    }
}
