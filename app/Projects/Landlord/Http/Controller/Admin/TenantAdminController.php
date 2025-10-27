<?php

namespace App\Projects\Landlord\Http\Controller\Admin;

use App\Attributes\RoutePrefix;
use App\Common\Admin\Controller\AdminController;
use App\Common\Repository\RepositoryManager;
use App\Common\Services\AlertManager;
use App\Projects\Landlord\Adapters\Admin\TenantAdmin;

#[RoutePrefix('tenants')]
class TenantAdminController extends AdminController
{
    public function __construct(
        protected RepositoryManager $repositoryManager,
        AlertManager $alertManager
    ) {
        $admin = new TenantAdmin($repositoryManager);
        parent::__construct($admin, $alertManager);
    }
}
