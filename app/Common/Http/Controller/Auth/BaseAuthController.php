<?php

namespace App\Common\Http\Controller\Auth;

use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

#[RoutePrefix('auth')]
abstract class BaseAuthController extends Controller
{
    /**
     * Nombre del guard a utilizar (e.g., 'landlord', 'web')
     */
    abstract protected function guard(): string;

    /**
     * Vista del formulario de login (e.g., 'landlord.auth.login')
     */
    abstract protected function loginView(): string;

    /**
     * Ruta de redirección tras login exitoso (e.g., '/landlord/admin/tenant/list')
     */
    abstract protected function defaultRedirect(): string;

    #[Route('/login', methods: ['GET'], name: 'login')]
    public function showLogin(): mixed
    {
        if (Auth::guard($this->guard())->check()) {
            return redirect($this->defaultRedirect());
        }

        return view($this->loginView());
    }

    #[Route('/login', methods: ['POST'], name: 'login.post')]
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard($this->guard())->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended($this->defaultRedirect());
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ])->onlyInput('email');
    }

    #[Route('/logout', methods: ['POST'], name: 'logout')]
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard($this->guard())->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($this->loginUrl());
    }

    /**
     * URL del login para redirección post-logout.
     * Por defecto deriva la URL a partir del defaultRedirect().
     * Las subclases pueden sobreescribir si la ruta difiere del patrón.
     */
    protected function loginUrl(): string
    {
        $parts = explode('/', trim($this->defaultRedirect(), '/'));

        return '/' . $parts[0] . '/auth/login';
    }
}
