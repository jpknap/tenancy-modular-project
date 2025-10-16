<?php

namespace App\Http\Controllers\Admin;

use App\Module\Admin\Adapter\AdminBaseAdapter;

abstract class AdminController
{
    public function __construct(
        private readonly AdminBaseAdapter $admin
    ) {
    }

    public function list()
    {
        return view('landlord.list', [
            'admin' => $this->admin,
        ]);
    }
}
