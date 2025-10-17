<?php

namespace App\Projects\Landlord\Http\Controller\Admin;

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
