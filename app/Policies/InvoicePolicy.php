<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return mixed
     */
    // public function view(User $user, Invoice $invoice)
    // {
    //     return true;
    // }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_invoices');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return mixed
     */
    // public function update(User $user, Invoice $invoice)
    // {
    //     return true;
    // }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return mixed
     */
    // public function delete(User $user, Invoice $invoice)
    // {
    //     return $user->hasPermission('delete_invoices');
    // }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return mixed
     */
    public function restore(User $user, Invoice $invoice)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return mixed
     */
    public function forceDelete(User $user, Invoice $invoice)
    {
        //
    }

    /**
     * Who can see a specific PO?
     */
    public function view(User $user, Invoice $invoice): bool
    {
        if (!$user->hasPermission('show_invoices') && !$user->hasPermission('browse_invoices')) {
            return false;
        }

        return in_array($invoice->user_id, $user->teamMemberIds());
    }

    /**
     * Who can edit a PO?
     * SP can edit ONLY if:
     *   - They created it
     *   - It's in draft or rejected state
     * SM/Admin can edit any time.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        if (!$user->hasPermission('edit_invoices')) {
            return false;
        }

        // SP can only edit their own POs in editable states
        if ($user->isSalesPerson()) {
            return $invoice->user_id === $user->id && $invoice->isEditable();
        }

        // SM can edit team POs, Admin can edit all
        return in_array($invoice->user_id, $user->teamMemberIds());
    }

    /**
     * Who can delete a PO?
     * Only SM (their team's POs) and Admin.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        if (!$user->hasPermission('delete_invoices')) {
            return false;
        }

        return in_array($invoice->user_id, $user->teamMemberIds());
    }

    /**
     * Who can approve/reject a PO?
     * Only SM (for their team) and Admin.
     */
    public function approve(User $user, Invoice $invoice): bool
    {
        if (!$user->hasPermission('approve_invoices')) {
            return false;
        }

        return in_array($invoice->user_id, $user->teamMemberIds());
    }
    public function export(User $user, Invoice $invoice): bool
    {
        if (!$user->hasPermission('export_reports')) {
            return false;
        }

        return in_array($invoice->user_id, $user->teamMemberIds());
    }
}
