<?php

namespace App\Policies;

use App\Models\Segment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SegmentPolicy
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
        return $user->hasPermission('browse_segments');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Segment  $segment
     * @return mixed
     */
    public function view(User $user, Segment $segment)
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
        return $user->hasPermission('create_segments');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Segment  $segment
     * @return mixed
     */
    public function update(User $user, Segment $segment)
    {
        return $user->hasPermission('edit_segments');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Segment  $segment
     * @return mixed
     */
    public function delete(User $user, Segment $segment)
    {
        return $user->hasPermission('delete_segments');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Segment  $segment
     * @return mixed
     */
    public function restore(User $user, Segment $segment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Segment  $segment
     * @return mixed
     */
    public function forceDelete(User $user, Segment $segment)
    {
        //
    }
}
