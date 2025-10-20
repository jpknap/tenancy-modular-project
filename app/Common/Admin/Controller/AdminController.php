<?php

namespace App\Common\Admin\Controller;

use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Common\Admin\Adapter\AdminBaseAdapter;
use Illuminate\Config\Repository;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $config = $this->admin->getListViewConfig();
        $items = $this->admin->paginate($config->getPerPage());

        return view('landlord.list', [
            'admin' => $this->admin,
            'items' => $items,
        ]);
    }

    #[Route('create', methods: ['GET','POST'], name: 'create')]
    public function new(Request $request)
    {
        $formRequestClass = $this->admin->getFormRequest();

        $formRequest = new $formRequestClass();
        if ($request->getMethod() === 'GET') {
            return view('landlord.create', [
                'admin' => $this->admin,
                'form' => $formRequest->getFormBuilder(),
            ]);
        }
        $validated = app($formRequestClass)->validated();
        $serviceClass = $this->admin->getService();

        $item = app($serviceClass)->create($validated);

        return redirect()
            ->route('landlord.admin.' . $this->admin->getRoutePrefix() . '.list')
            ->with('success', 'Registro creado exitosamente');
    }
}
