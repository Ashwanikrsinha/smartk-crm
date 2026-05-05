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
     * SP edits own POs only in draft/rejected/bm_rejected state.
     * SM edits team POs any time before BM approval.
     * Admin edits all.
     * BM cannot edit.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        if (!$user->hasPermission('edit_invoices')) return false;

        if ($user->isSalesPerson()) {
            return $invoice->user_id === $user->id && $invoice->isEditable();
        }

        if ($user->isSalesManager()) {
            return in_array($invoice->user_id, $user->teamMemberIds())
                && in_array($invoice->status, [
                    Invoice::STATUS_DRAFT,
                    Invoice::STATUS_SUBMITTED,
                    Invoice::STATUS_REJECTED,
                    Invoice::STATUS_BM_REJECTED,
                ]);
        }

        // Admin
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
     * SM approves: submitted → sm_approved
     * BM approves: sm_approved → approved
     * Each role can only act at their level.
     */
    public function approve(User $user, Invoice $invoice): bool
    {
        // SM: can approve submitted POs from their team
        if ($user->isSalesManager() && $user->hasPermission('approve_invoices')) {
            return $invoice->isSubmitted()
                && in_array($invoice->user_id, $user->teamMemberIds());
        }

        // BM: can approve sm_approved POs from ANYONE
        if ($user->isBusinessManager() && $user->hasPermission('bm_approve_invoices')) {
            return $invoice->isSmApproved();
        }

        // Admin can do both
        if ($user->isAdmin()) {
            return $invoice->isSubmitted() || $invoice->isSmApproved();
        }

        return false;
    }
    public function export(User $user, Invoice $invoice): bool
    {
        if (!$user->hasPermission('export_reports')) {
            return false;
        }

        return in_array($invoice->user_id, $user->teamMemberIds());
    }
}
