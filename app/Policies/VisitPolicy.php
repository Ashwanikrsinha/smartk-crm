<?php

namespace App\Policies;

use App\Models\Visit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VisitPolicy
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
        return $user->hasPermission('browse_visits');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Visit  $visit
     * @return mixed
     */
    public function view(User $user, Visit $visit)
    {
        return $user->hasPermission('show_visits');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_visits');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Visit  $visit
     * @return mixed
     */
    public function update(User $user, Visit $visit)
    {
        return $user->hasPermission('edit_visits');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Visit  $visit
     * @return mixed
     */
    public function delete(User $user, Visit $visit)
    {
        return $user->hasPermission('delete_visits');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Visit  $visit
     * @return mixed
     */
    public function restore(User $user, Visit $visit)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Visit  $visit
     * @return mixed
     */
    public function forceDelete(User $user, Visit $visit)
    {
        //
    }
}
