<?php

namespace App\Policies;

use App\Models\Transport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransportPolicy
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
       return $user->hasPermission('browse_transports');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transport  $transport
     * @return mixed
     */
    public function view(User $user, Transport $transport)
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
       return $user->hasPermission('create_transports');

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transport  $transport
     * @return mixed
     */
    public function update(User $user, Transport $transport)
    {
       return $user->hasPermission('edit_transports');

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transport  $transport
     * @return mixed
     */
    public function delete(User $user, Transport $transport)
    {
      return  $user->hasPermission('delete_transports');

    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transport  $transport
     * @return mixed
     */
    public function restore(User $user, Transport $transport)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transport  $transport
     * @return mixed
     */
    public function forceDelete(User $user, Transport $transport)
    {
        //
    }
}