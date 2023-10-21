<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: "api")]
class ApiLoginController extends AbstractController
{
    // TODO refresh access token
}
