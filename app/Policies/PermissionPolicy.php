<?php

namespace App\Policies;

use App\Models\Admin\Permission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\ApiPolicy;

class PermissionPolicy extends ApiPolicy
{
    use HandlesAuthorization;

    /**
     * Permission related to the permissions model.
     * use ApiPolicy
     * @var string
     */
    public $model = "permissions";

}
