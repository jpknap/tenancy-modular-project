<?php

namespace App\Common\Admin\Controller;

use App\Attributes\Route;
use App\Attributes\RoutePrefix;
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
        $config = $this->admin->getListViewConfig();
        $items = $this->admin->paginate($config->getPerPage());

        return view('landlord.list', [
            'admin' => $this->admin,
            'items' => $items,
        ]);
    }

    #[Route('new', methods: ['GET'], name: 'new')]
    public function new()
    {
        $formRequestClass = $this->admin->getFormRequest();

        // Crear instancia directa sin validación (solo para obtener el form builder)
        $formRequest = new $formRequestClass();

        return view('landlord.new', [
            'admin' => $this->admin,
            'form' => $formRequest->getFormBuilder(),
        ]);
    }

    #[Route('new', methods: ['POST'], name: 'store')]
    public function store()
    {
        $formRequestClass = $this->admin->getFormRequest();
        $validated = app($formRequestClass)->validated();
        $serviceClass = $this->admin->getService();

        $item = app($serviceClass)->create($validated);

        return redirect()
            ->route('landlord.admin.' . $this->admin->getRoutePrefix() . '.list')
            ->with('success', 'Registro creado exitosamente');
    }
}
