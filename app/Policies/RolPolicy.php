<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\ApiPolicy;


class RolPolicy extends ApiPolicy
{
    use HandlesAuthorization;
    
    /**
     * Permission related to the roles model.
     * use ApiPolicy
     * @var string
     */
    public $model = "roles";


    /**
     * Determine whether the user can assign permissions to model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function assignPermissions(User $user)
    {
        foreach ($user->roles as $role) {
            if ($role->hasPermissionTo('roles.assign-permissions')) {
                return true;
            } else {
                continue;
            }
        }
        //return $user->hasDirectPermission('roles.assign-permissions');
    }

}
