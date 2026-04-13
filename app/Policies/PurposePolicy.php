<?php

namespace App\Policies;

use App\Models\Purpose;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurposePolicy
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
       return $user->hasPermission('browse_purposes');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Purpose  $purpose
     * @return mixed
     */
    public function view(User $user, Purpose $purpose)
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
       return $user->hasPermission('create_purposes');

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Purpose  $purpose
     * @return mixed
     */
    public function update(User $user, Purpose $purpose)
    {
       return $user->hasPermission('edit_purposes');

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Purpose  $purpose
     * @return mixed
     */
    public function delete(User $user, Purpose $purpose)
    {
      return  $user->hasPermission('delete_purposes');

    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Purpose  $purpose
     * @return mixed
     */
    public function restore(User $user, Purpose $purpose)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Purpose  $purpose
     * @return mixed
     */
    public function forceDelete(User $user, Purpose $purpose)
    {
        //
    }
}
