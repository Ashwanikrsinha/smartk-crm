<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PoDocumentService
{
    /**
     * Generate the PO DOCX, save it permanently to storage,
     * update the invoice record with the file path, and return the path.
     *
     * Storage location: storage/app/public/po-documents/{year}/{po_number}.docx
     * Accessible via: /storage/po-documents/{year}/{po_number}.docx
     */
    public function generate(Invoice $invoice): string
    {
        $invoice->loadMissing([
            'customer.contacts.designation',
            'invoiceItems.product.category',
            'pdcs',
            'user',
            'approvedBy',
        ]);

        $data = $this->buildData($invoice);

        // ── Temp files for generation ────────────────────
        $tmpId      = $invoice->id . '_' . time();
        $jsonPath   = sys_get_temp_dir() . "/po_data_{$tmpId}.json";
        $tmpDocx    = sys_get_temp_dir() . "/po_output_{$tmpId}.docx";

        file_put_contents($jsonPath, json_encode($data, JSON_UNESCAPED_UNICODE));

        $scriptPath = base_path('scripts/generate_po_docx.js');

        $command = sprintf(
            'node %s %s %s 2>&1',
            escapeshellarg($scriptPath),
            escapeshellarg($jsonPath),
            escapeshellarg($tmpDocx)
        );

        $output   = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        @unlink($jsonPath); // always clean up JSON

        if ($exitCode !== 0 || !file_exists($tmpDocx)) {
            throw new \RuntimeException(
                'PO DOCX generation failed: ' . implode("\n", $output)
            );
        }

        // ── Persist to permanent storage ─────────────────
        $year         = now()->format('Y');
        $safePoNumber = Str::slug($invoice->po_number); // PO-2025-0001
        $storagePath  = "po-documents/{$year}/{$safePoNumber}.docx";

        // Save to storage/app/public/ (accessible via symlink)
        Storage::disk('public')->put($storagePath, file_get_contents($tmpDocx));

        @unlink($tmpDocx); // clean up temp DOCX

        // ── Update invoice record with stored path ────────
        $invoice->updateQuietly(['po_document_path' => $storagePath]);

        return $storagePath;
    }

    /**
     * Get the full filesystem path of the stored document.
     * Returns null if not generated yet.
     */
    public function storagePath(Invoice $invoice): ?string
    {
        if (!$invoice->po_document_path) return null;

        $fullPath = Storage::disk('public')->path($invoice->po_document_path);

        return file_exists($fullPath) ? $fullPath : null;
    }

    /**
     * Get the public download URL for the stored document.
     */
    public function publicUrl(Invoice $invoice): ?string
    {
        if (!$invoice->po_document_path) return null;

        if (!Storage::disk('public')->exists($invoice->po_document_path)) return null;

        return Storage::disk('public')->url($invoice->po_document_path);
    }

    /**
     * Check whether the document exists in storage.
     */
    public function exists(Invoice $invoice): bool
    {
        return $invoice->po_document_path &&
            Storage::disk('public')->exists($invoice->po_document_path);
    }

    /**
     * Regenerate the document (e.g. after data correction).
     * Overwrites the existing file.
     */
    public function regenerate(Invoice $invoice): string
    {
        return $this->generate($invoice);
    }
    private function buildData(Invoice $invoice): array
    {
        $customer = $invoice->customer;
        $contact  = $customer->contacts->first();

        $items = $invoice->invoiceItems->map(function ($item) {
            $categoryName = $item->product->category?->name;
            $name = $categoryName
                ? "{$categoryName} - {$item->product->name}"
                : $item->product->name;

            return [
                'name'  => $name,
                'rate'  => number_format($item->rate, 2),
                'qty'   => $item->quantity,
                'total' => number_format($item->quantity * $item->rate, 2),
            ];
        })->toArray();

        $pdcs = $invoice->pdcs->map(function ($pdc, $idx) {
            return [
                'label'         => $pdc->pdc_label ?? ('PDC ' . ($idx + 1)),
                'cheque_number' => $pdc->cheque_number,
                'amount'        => number_format($pdc->amount, 2),
                'date'          => $pdc->cheque_date->format('d F, Y'),
            ];
        })->toArray();

        return [
            'po_number'           => $invoice->po_number,
            'po_date'             => $invoice->approved_at
                ? $invoice->approved_at->format('d F, Y')
                : now()->format('d F, Y'),
            'school_name'         => $customer->name,
            'school_address'      => $customer->address ?? '',
            'school_city'         => $customer->city    ?? '',
            'school_state'        => $customer->state   ?? '',
            'contact_name'        => $contact?->name                   ?? '',
            'contact_designation' => $contact?->designation?->name     ?? '',
            'contact_phone'       => $contact?->contact_number         ?? $customer->phone_number ?? '',
            'contact_email'       => $customer->email                  ?? '',
            'items'               => $items,
            'grand_total'         => number_format($invoice->amount, 2),
            'pdcs'                => $pdcs,
            'remarks'             => $invoice->remarks ?? '',
        ];
    }
}
