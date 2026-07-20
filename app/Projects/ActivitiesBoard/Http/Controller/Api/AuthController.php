<?php

namespace App\Projects\ActivitiesBoard\Http\Controller\Api;

use App\Attributes\Middleware;
use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Common\Http\Controller\Api\BaseApiAuthController;
use App\Common\Http\Controller\Api\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[RoutePrefix('api/auth')]
class AuthController extends BaseApiAuthController
{
    #[Route('/login', methods: ['POST'], name: 'login')]
    #[Middleware(['throttle:login'])]
    public function login(LoginRequest $request): JsonResponse
    {
        return parent::login($request);
    }

    #[Route('/logout', methods: ['POST'], name: 'logout')]
    #[Middleware(['auth:sanctum'])]
    public function logout(Request $request): JsonResponse
    {
        return parent::logout($request);
    }

    #[Route('/me', methods: ['GET'], name: 'me')]
    #[Middleware(['auth:sanctum'])]
    public function me(Request $request): JsonResponse
    {
        return parent::me($request);
    }

    protected function guard(): string
    {
        return 'web';
    }
}
