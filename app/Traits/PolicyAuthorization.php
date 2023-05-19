<?php

namespace App\Traits;
use App\Models\User;

trait PolicyAuthorization
{
    
    /**
     * Determines the model assigning for the permissions
     *
     * @var string
     */
    public $model;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        $permission = $this->model ? "$this->model.index":"index";
        return $user->can($permission);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {
        $permission = $this->model ? "$this->model.show":"show";
        return $user->can($permission);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        $permission = $this->model ? "$this->model.store":"store";
        return $user->can($permission);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        $permission = $this->model ? "$this->model.update":"update";
        return $user->can($permission);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user)
    {
        $permission = $this->model ? "$this->model.destroy":"destroy";
        return $user->can($permission);
    }
}

