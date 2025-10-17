<?php

namespace App\Projects\Landlord\Http\Controller\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()
                ->regenerate();
            return redirect()->intended('/landlord/admin/tenant/list');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()
            ->invalidate();
        $request->session()
            ->regenerateToken();

        return redirect('/landlord/login');
    }
}
