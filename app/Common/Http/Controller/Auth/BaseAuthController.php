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
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard($this->guard())->attempt($credentials)) {
            $request->session()
                ->regenerate();

            return redirect()->intended($this->defaultRedirect());
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    #[Route('/logout', methods: ['POST'], name: 'logout')]
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard($this->guard())->logout();
        $request->session()
            ->invalidate();
        $request->session()
            ->regenerateToken();

        return redirect(route($this->loginRoute()));
    }

    /**
     * Nombre del guard a utilizar (e.g., 'landlord', 'web')
     */
    abstract protected function guard(): string;

    /**
     * Vista del formulario de login (e.g., 'landlord.auth.login')
     */
    abstract protected function loginView(): string;

    /**
     * Nombre de ruta nombrada para redirección tras login exitoso (e.g., 'landlord.admin.tenants.list')
     */
    abstract protected function defaultRedirectRoute(): string;

    /**
     * Nombre de ruta nombrada para redirección tras logout (e.g., 'landlord.auth.login')
     */
    abstract protected function loginRoute(): string;

    protected function defaultRedirect(): string
    {
        return route($this->defaultRedirectRoute());
    }
}
