<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SchoolDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Upload one or more documents for a school.
     * Called via AJAX from school show/edit page OR from new-school-modal.
     *
     * POST /school-documents
     * Params: customer_id, type (aadhar|pan|gst_certificate), file
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type'        => 'required|in:aadhar,pan,gst_certificate',
            'file'        => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        // Delete existing doc of same type (one per type per school)
        $existing = SchoolDocument::where('customer_id', $customer->id)
            ->where('type', $request->type)
            ->first();

        if ($existing) {
            Storage::disk('public')->delete($existing->filename);
            $existing->delete();
        }

        // Store file: school-docs/{school_code}/{type}.{ext}
        $ext      = $request->file('file')->getClientOriginalExtension();
        $path     = $request->file('file')->storeAs(
            "school-docs/{$customer->school_code}",
            "{$request->type}.{$ext}",
            'public'
        );

        $doc = SchoolDocument::create([
            'customer_id' => $customer->id,
            'type'        => $request->type,
            'filename'    => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json([
            'success'  => true,
            'doc_id'   => $doc->id,
            'url'      => Storage::disk('public')->url($path),
            'type'     => $request->type,
            'message'  => ucwords(str_replace('_', ' ', $request->type)) . ' uploaded successfully.',
        ]);
    }

    /**
     * Handle bulk document upload from new-school-modal.
     * Called after school is created, uploads aadhar/pan/gst_certificate.
     *
     * POST /school-documents/bulk
     * Params: customer_id, aadhar (file), pan (file), gst_certificate (file)
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'aadhar'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pan'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'gst_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $uploaded = [];

        foreach (SchoolDocument::types() as $type) {
            if ($request->hasFile($type)) {

                $ext  = $request->file($type)->getClientOriginalExtension();
                $path = $request->file($type)->storeAs(
                    "school-docs/{$customer->school_code}",
                    "{$type}.{$ext}",
                    'public'
                );

                SchoolDocument::create([
                    'customer_id' => $customer->id,
                    'type'        => $type,
                    'filename'    => $path,
                    'uploaded_by' => auth()->id(),
                ]);

                $uploaded[] = $type;
            }
        }

        return response()->json([
            'success'  => true,
            'uploaded' => $uploaded,
        ]);
    }

    /**
     * Delete a document.
     * DELETE /school-documents/{doc}
     */
    public function destroy(SchoolDocument $doc)
    {
        $this->authorize('update', $doc->customer); // only allowed editors can delete

        Storage::disk('public')->delete($doc->filename);
        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted.',
        ]);
    }

    /**
     * View/download a document (returns redirect to storage URL).
     * GET /school-documents/{doc}/download
     */
    public function download(SchoolDocument $doc)
    {
        $this->authorize('view', $doc->customer);

        if (!Storage::disk('public')->exists($doc->filename)) {
            abort(404, 'Document not found.');
        }

        return response()->download(
            Storage::disk('public')->path($doc->filename),
            basename($doc->filename)
        );
    }
}
