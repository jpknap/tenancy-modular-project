<?php

namespace App\Common\Admin\Controller;

use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\ProjectManager;
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

    #[Route('create', methods: ['GET', 'POST'], name: 'create')]
    public function create(Request $request)
    {
        $config = $this->admin->getCreateViewConfig();

        if ($request->getMethod() === 'GET') {
            return view('landlord.new', [
                'admin' => $this->admin,
                'form' => $this->admin->getCreateViewConfig()
                    ->getFormBuilder(),
                'config' => $config,
            ]);
        }
        $formRequestClass = $this->admin->getFormRequest();
        $validated = app($formRequestClass)
            ->validated();

        $serviceClass = $this->admin->getService();

        app($serviceClass)
            ->create($validated);
        $project = ProjectManager::getCurrentProject()->getPrefix();
        return redirect()->route("{$project}.admin.{$this->admin->getRoutePrefix()}.list");
    }

    #[Route('edit/{id}', methods: ['GET', 'PUT'], name: 'edit')]
    public function edit(Request $request, $id)
    {
        $item = $this->admin->find($id);

        if (! $item) {
            abort(404);
        }

        $config = $this->admin->getEditViewConfig($item);

        if ($request->isMethod('GET')) {
            return view('landlord.edit', [
                'admin' => $this->admin,
                'config' => $config,
            ]);
        }

        $formRequestClass = $this->admin->getFormRequest();
        $validated = app($formRequestClass)
            ->validated();
        $serviceClass = $this->admin->getService();

        $updatedItem = app($serviceClass)
            ->update($id, $validated);

        return redirect()
            ->route($this->admin->getUrlName('list'))
            ->with('success', 'Registro actualizado exitosamente');
    }

    #[Route('destroy/{id}', methods: ['DELETE'], name: 'destroy')]
    public function destroy($id)
    {
        $item = $this->admin->find($id);

        if (! $item) {
            abort(404);
        }

        $serviceClass = $this->admin->getService();
        app($serviceClass)
            ->delete($id);
        return redirect()->route($this->admin->getUrlName('list'));
    }
}
