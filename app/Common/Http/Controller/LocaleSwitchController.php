<?php

namespace App\Common\Http\Controller;

use App\Common\Services\LocaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;

class LocaleSwitchController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        if (in_array($locale, LocaleService::SUPPORTED, true)) {
            session([
                'locale' => $locale,
            ]);
            App::setLocale($locale);
        }

        return redirect()->back();
    }
}
