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
        if (!$user->hasPermission('show_visits')) {
            return false;
        }

        return in_array($visit->user_id, $user->teamMemberIds());
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
         if (!$user->hasPermission('edit_visits')) {
            return false;
        }

        // SP can only edit their own visits
        if ($user->isSalesPerson()) {
            return $visit->user_id === $user->id;
        }

        return in_array($visit->user_id, $user->teamMemberIds());
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
        if (!$user->hasPermission('delete_visits')) {
            return false;
        }

        return in_array($visit->user_id, $user->teamMemberIds());
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
