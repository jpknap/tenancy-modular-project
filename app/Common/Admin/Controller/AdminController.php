<?php

namespace App\Common\Admin\Controller;

use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Common\Admin\Adapter\AdminBaseAdapter;
use Illuminate\Http\Request;
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

    #[Route('create', methods: ['GET', 'POST'], name: 'create')]
    public function create(Request $request)
    {
        if ($request->isMethod('GET')) {
            $config = $this->admin->getCreateViewConfig();

            return view('landlord.new', [
                'admin' => $this->admin,
                'form' => $config->getFormBuilder(),
                'config' => $config,
            ]);
        }

        $formRequestClass = $this->admin->getFormRequest();
        $validated = app($formRequestClass)->validated();

        resolve($this->admin->getService())->create($validated);

        return redirect()
            ->route($this->admin->getUrlName('list'))
            ->with('success', 'Registro creado exitosamente');
    }

    #[Route('edit/{id}', methods: ['GET', 'PUT'], name: 'edit')]
    public function edit(Request $request, int $id)
    {
        $item = $this->admin->find($id);

        if (! $item) {
            abort(404);
        }

        if ($request->isMethod('GET')) {
            $config = $this->admin->getEditViewConfig($item);

            return view('landlord.edit', [
                'admin' => $this->admin,
                'config' => $config,
            ]);
        }

        $formRequestClass = $this->admin->getFormRequest();
        $validated = app($formRequestClass)->validated();

        resolve($this->admin->getService())->update($id, $validated);

        return redirect()
            ->route($this->admin->getUrlName('list'))
            ->with('success', 'Registro actualizado exitosamente');
    }

    #[Route('destroy/{id}', methods: ['DELETE'], name: 'destroy')]
    public function destroy(int $id)
    {
        $item = $this->admin->find($id);

        if (! $item) {
            abort(404);
        }

        resolve($this->admin->getService())->delete($id);

        return redirect()
            ->route($this->admin->getUrlName('list'))
            ->with('success', 'Registro eliminado exitosamente');
    }
}
