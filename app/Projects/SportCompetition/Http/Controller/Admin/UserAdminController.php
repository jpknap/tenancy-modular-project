<?php

namespace App\Projects\SportCompetition\Http\Controller\Admin;

use App\Attributes\RoutePrefix;
use App\Common\Admin\Controller\AdminController;
use App\Common\Repository\RepositoryManager;
use App\Common\Services\AlertManager;
use App\Projects\SportCompetition\Adapters\Admin\UserAdmin;

#[RoutePrefix('users')]
class UserAdminController extends AdminController
{
    public function __construct(
        protected RepositoryManager $repositoryManager,
        AlertManager $alertManager
    ) {
        $admin = new UserAdmin($repositoryManager);
        parent::__construct($admin, $alertManager);
    }
}
