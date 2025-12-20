<?php

namespace App\Projects\ActivitiesBoard\Http\Controller\Auth;

use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

#[RoutePrefix('auth')]
class AuthController extends Controller
{
    #[Route('/login', methods: ['GET'], name: 'login')]
    public function showLogin()
    {
        return view('activities-board.auth.login');
    }

    #[Route('/login', methods: ['POST'], name: 'login.post')]
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()
                ->regenerate();
            return redirect()->intended('/activities-board/admin/users/list');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ]);
    }

    #[Route('/logout', methods: ['POST'], name: 'logout')]
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()
            ->invalidate();
        $request->session()
            ->regenerateToken();

        return redirect('/activities-board/auth/login');
    }
}
