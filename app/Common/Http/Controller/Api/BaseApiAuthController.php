<?php

namespace App\Common\Http\Controller\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

/**
 * Template method de autenticación por token (Sanctum), análogo a
 * BaseAuthController pero stateless: sin sesión, sin cookies, sin CSRF.
 *
 * Las rutas (#[Route], #[RoutePrefix], #[Middleware]) se declaran en cada
 * controller concreto y no acá, porque el rate limit del login
 * (#[Middleware(['throttle:login'])]) necesita poder aplicarse por
 * controller concreto; eso obliga a que cada uno override los métodos
 * (delegando en la implementación de esta clase) para poder repetir sus
 * propios atributos de ruta.
 */
abstract class BaseApiAuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $modelClass = $this->userModelClass();

        /** @var \App\Models\User|null $user */
        $user = $modelClass::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas.',
            ], 401);
        }

        if ($user->enabled !== true) {
            return response()->json([
                'message' => 'Usuario deshabilitado.',
            ], 403);
        }

        $token = $user->createToken($request->userAgent() ?? 'api');

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Sesión cerrada.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Nombre del guard a utilizar (e.g., 'landlord', 'web')
     */
    abstract protected function guard(): string;

    /**
     * Resuelve el FQCN del modelo Eloquent configurado como provider del guard,
     * igual que lo hace internamente Auth::guard($guard)->attempt().
     */
    protected function userModelClass(): string
    {
        $provider = config("auth.guards.{$this->guard()}.provider");

        return config("auth.providers.{$provider}.model");
    }
}
