<?php

namespace App\Http\Requests;

// Identical validation rules for update
// Extend StoreRequest to avoid duplication
class InvoiceUpdateRequest extends InvoiceStoreRequest
{
    // All rules are the same — store and update share the same validation.
    // Policy handles the edit-lock (approved POs cannot be updated).
}
