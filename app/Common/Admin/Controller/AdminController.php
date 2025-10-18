<?php

namespace App\Common\Admin\Controller;

use App\Attributes\RoutePrefix;
use App\Attributes\Route;
use App\Common\Admin\Adapter\AdminBaseAdapter;
use Illuminate\Routing\Controller;

#[RoutePrefix('admin')]
abstract class AdminController extends Controller
{
    public function __construct(
        private readonly AdminBaseAdapter $admin
    ) {
    }

    #[Route('list', methods: ['GET'], name: 'list')]
    public function list()
    {
        return view('landlord.list', [
            'admin' => $this->admin,
        ]);
    }
}
