<?php

namespace App\Common\Admin\Controller;

use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\Services\AlertManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

#[RoutePrefix('admin')]
abstract class AdminController extends Controller
{
    public function __construct(
        private readonly AdminBaseAdapter $admin,
        private readonly AlertManager $alertManager
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

        $this->alertManager->success(
            'El registro ha sido creado correctamente',
            '¡Registro creado!'
        );

        return redirect()->route($this->admin->getUrlName('list'));
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

        $this->alertManager->success(
            'Los cambios han sido guardados correctamente',
            '¡Registro actualizado!'
        );

        return redirect()->route($this->admin->getUrlName('list'));
    }

    #[Route('delete/{id}', methods: ['GET', 'DELETE'], name: 'delete')]
    public function delete(Request $request, int $id)
    {
        $item = $this->admin->find($id);

        if (! $item) {
            abort(404);
        }

        if ($request->isMethod('GET')) {
            $config = $this->admin->getDeleteViewConfig($item);

            return view('landlord.delete', [
                'admin' => $this->admin,
                'config' => $config,
            ]);
        }

        resolve($this->admin->getService())->delete($id);

        $this->alertManager->success(
            'El registro ha sido eliminado permanentemente',
            '¡Registro eliminado!'
        );

        return redirect()->route($this->admin->getUrlName('list'));
    }
}
