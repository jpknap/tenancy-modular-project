<?php

namespace App\Common\Admin\Controller;

use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\ProjectManager;
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
        $config = $this->admin->getCreateViewConfig();

        if ($request->getMethod() === 'GET') {
            return view('landlord.new', [
                'admin' => $this->admin,
                'form' => $this->admin->getCreateViewConfig()->getFormBuilder(),
                'config' => $config,
            ]);
        }
        $formRequestClass = $this->admin->getFormRequest();
        $validated = app($formRequestClass)->validated();

        $serviceClass = $this->admin->getService();

        app($serviceClass)->create($validated);
        $project = ProjectManager::getCurrentProject()->getPrefix();
        return redirect()->route("{$project}.admin.{$this->admin->getRoutePrefix()}.list");
    }
}
