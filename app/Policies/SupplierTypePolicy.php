<?php

namespace App\Policies;

use App\Models\SupplierType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
       return $user->hasPermission('browse_supplier_types');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SupplierType  $type
     * @return mixed
     */
    public function view(User $user, SupplierType $type)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
       return $user->hasPermission('create_supplier_types');

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SupplierType  $type
     * @return mixed
     */
    public function update(User $user, SupplierType $type)
    {
       return $user->hasPermission('edit_supplier_types');

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SupplierType  $type
     * @return mixed
     */
    public function delete(User $user, SupplierType $type)
    {
      return  $user->hasPermission('delete_supplier_types');

    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SupplierType  $type
     * @return mixed
     */
    public function restore(User $user, SupplierType $type)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SupplierType  $type
     * @return mixed
     */
    public function forceDelete(User $user, SupplierType $type)
    {
        //
    }
}
