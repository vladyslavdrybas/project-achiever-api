<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: "app")]
class MainController extends AbstractController
{
    #[Route("/", name: "_homepage", methods: ["GET", "OPTIONS", "HEAD"])]
    public function index(): JsonResponse
    {
        return $this->json(["message" => "welcome"]);
    }
}
