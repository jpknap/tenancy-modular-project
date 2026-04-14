<?php

namespace App\Common\Http\Controller;

use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Common\Http\Requests\ProfileFormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

#[RoutePrefix('profile')]
abstract class ProfileController extends Controller
{
    abstract protected function guard(): string;

    abstract protected function profileView(): string;

    abstract protected function profileRoute(): string;

    #[Route('/edit', methods: ['GET'], name: 'edit')]
    public function show(): mixed
    {
        $user        = Auth::guard($this->guard())->user();
        $formRequest = new ProfileFormRequest();
        $form        = $formRequest->getFormBuilder();

        return view($this->profileView(), [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/edit', methods: ['PUT'], name: 'edit.put')]
    public function update(ProfileFormRequest $request): RedirectResponse
    {
        $user = Auth::guard($this->guard())->user();

        $data = $request->only('name', 'email', 'locale');
        $user->update($data);

        if (! empty($data['locale'])) {
            session(['locale' => $data['locale']]);
        }

        return redirect()
            ->route($this->profileRoute())
            ->with('success', __('profile.updated'));
    }
}
