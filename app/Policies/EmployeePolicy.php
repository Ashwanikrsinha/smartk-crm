<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('browse_employees');
        
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Employee  $model
     * @return mixed
     */
    public function view(User $user, Employee $model)
    {
        return $user->hasPermission('show_employees');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_employees');
        
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Employee  $model
     * @return mixed
     */
    public function update(User $user, Employee $model)
    {
        return $user->hasPermission('edit_employees');
        
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Employee  $model
     * @return mixed
     */
    public function delete(User $user, Employee $model)
    {
        return $user->hasPermission('delete_employees');
        
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Employee  $model
     * @return mixed
     */
    public function restore(User $user, Employee $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Employee  $model
     * @return mixed
     */
    public function forceDelete(User $user, Employee $model)
    {
        //
    }
}
