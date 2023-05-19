<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\ApiPolicy;


class UserPolicy extends ApiPolicy
{
    use HandlesAuthorization;
  
    /**
     * Permission related to the users model.
     * use ApiPolicy
     * @var string
     */
    public $model = "users";

    /**
     * Determine whether the user can assign rol to the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function assignRole(User $user)
    {
        return $user->can('users.assign-role');
    }

    /**
     * Determine whether the user can assign permission to the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function assignPermission(User $user)
    {
        return $user->can('users.assign-permissions');
    }

    /**
     * Determine whether the user can revoke permission to the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function revokePermission(User $user)
    {
        return $user->can('users.revoke-permissions');

    }

    /**
     * Determine whether the user can update password to the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updatePassword(User $user)
    {
        return $user->can('users.update-password');
    }
}
