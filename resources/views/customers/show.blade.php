@extends('layouts.dashboard')
@section('content')

    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">{{ $customer->name }}</h5>
            <small class="text-muted">{{ $customer->school_code }}</small>
        </div>
        <div class="d-flex gap-2">
            @can('update', $customer)
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">
                    <i class="feather icon-edit-2 me-1"></i> Edit
                </a>
            @endcan
            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">
                <i class="feather icon-arrow-left me-1"></i> Back
            </a>
        </div>
    </header>

    {{-- ═══ FINANCIAL SUMMARY ════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        @php
            $fw = [
                ['label' => 'Total PO Amount', 'value' => $financials['total_po'], 'color' => 'warning'],
                ['label' => 'Total Billed', 'value' => $financials['total_billed'], 'color' => 'info'],
                ['label' => 'Total Collected', 'value' => $financials['total_collected'], 'color' => 'success'],
                ['label' => 'Outstanding', 'value' => $financials['total_outstanding'], 'color' => 'danger'],
            ];
        @endphp
        @foreach ($fw as $w)
            <div class="col-6 col-lg-3">
                <div class="bg-white rounded shadow-sm p-3 text-center border-top border-{{ $w['color'] }} border-3">
                    <h5 class="fw-bold text-{{ $w['color'] }} mb-1">₹{{ number_format($w['value'], 2) }}</h5>
                    <small class="text-muted">{{ $w['label'] }}</small>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">

        {{-- ═══ SCHOOL DETAILS ══════════════════════════════════ --}}
        <div class="col-lg-5">
            <div class="bg-white rounded shadow-sm p-4 mb-3">
                <h6 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="feather icon-home me-2 text-primary"></i> School Information
                </h6>
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <th class="text-muted" style="width:40%">School Code</th>
                            <td><strong class="text-primary">{{ $customer->school_code }}</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted">School Name</th>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Phone</th>
                            <td>{{ $customer->phone_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Email</th>
                            <td>{{ $customer->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Address</th>
                            <td>{{ $customer->address ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">City / State</th>
                            <td>{{ $customer->city }}, {{ $customer->state }}
                                {{ $customer->pin_code ? "- {$customer->pin_code}" : '' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">GSTIN</th>
                            <td>
                                @if ($customer->gstin)
                                    <code>{{ $customer->gstin }}</code>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Lead Source</th>
                            <td>
                                @if ($customer->leadSource)
                                    <span class="badge bg-info">{{ $customer->leadSource->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Registered By</th>
                            <td>{{ $customer->createdBy?->username ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- ═══ CONTACTS ══════════════════════════════════════ --}}
            @if ($customer->contacts->count())
                <div class="bg-white rounded shadow-sm p-4 mb-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="feather icon-user me-2 text-primary"></i> Contact Persons
                    </h6>
                    @foreach ($customer->contacts as $contact)
                        <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                            <div>
                                <strong>{{ $contact->name }}</strong>
                                <small class="text-muted d-block">{{ $contact->contact_number }}</small>
                            </div>
                            <span class="badge bg-secondary">{{ $contact->designation?->name ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ═══ DOCUMENTS ════════════════════════════════════════ --}}
        <div class="col-lg-7">
            <div class="bg-white rounded shadow-sm p-4 mb-3">
                <h6 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="feather icon-file me-2 text-primary"></i> School Documents
                </h6>

                @php
                    $docMap = $customer->documents->keyBy('type');
                    $docLabels = [
                        'aadhar' => ['label' => 'Aadhar Card', 'icon' => 'icon-credit-card'],
                        'pan' => ['label' => 'PAN Card', 'icon' => 'icon-credit-card'],
                        'gst_certificate' => ['label' => 'GST Certificate', 'icon' => 'icon-file-text'],
                    ];
                @endphp

                <div class="row g-3">
                    @foreach ($documentTypes as $type)
                        @php $doc = $docMap[$type] ?? null; @endphp
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center h-100">
                                <i
                                    class="feather {{ $docLabels[$type]['icon'] }} fs-3 mb-2
                           {{ $doc ? 'text-success' : 'text-muted' }}"></i>
                                <p class="mb-2 small fw-bold">{{ $docLabels[$type]['label'] }}</p>

                                @if ($doc)
                                    <a href="{{ route('school-documents.download', $doc) }}"
                                        class="btn btn-sm btn-outline-success w-100 mb-1">
                                        <i class="feather icon-download me-1"></i> Download
                                    </a>
                                    @can('update', $customer)
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 upload-doc-btn"
                                            data-type="{{ $type }}" data-customer="{{ $customer->id }}">
                                            <i class="feather icon-refresh-cw me-1"></i> Replace
                                        </button>
                                    @endcan
                                @else
                                    @can('update', $customer)
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 upload-doc-btn"
                                            data-type="{{ $type }}" data-customer="{{ $customer->id }}">
                                            <i class="feather icon-upload me-1"></i> Upload
                                        </button>
                                    @else
                                        <span class="badge bg-secondary">Not uploaded</span>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Hidden file inputs per doc type --}}
                @can('update', $customer)
                    @foreach ($documentTypes as $type)
                        <input type="file" id="file-{{ $type }}" class="d-none doc-file-input"
                            accept=".pdf,.jpg,.jpeg,.png" data-type="{{ $type }}" data-customer="{{ $customer->id }}">
                    @endforeach
                @endcan
            </div>

            {{-- ═══ PO HISTORY ════════════════════════════════════ --}}
            <div class="bg-white rounded shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h6 class="fw-bold mb-0">
                        <i class="feather icon-file-text me-2 text-primary"></i>
                        Purchase Order History
                        <span class="badge bg-secondary ms-1">{{ $customer->invoices->count() }}</span>
                    </h6>
                    @can('create', App\Models\Invoice::class)
                        <a href="{{ route('invoices.create') }}?customer_id={{ $customer->id }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="feather icon-plus me-1"></i> New PO
                        </a>
                    @endcan
                </div>

                @forelse($customer->invoices as $po)
                    <div class="d-flex justify-content-between align-items-start border-bottom py-2">
                        <div>
                            <a href="{{ route('invoices.show', $po) }}" class="text-primary fw-bold">
                                {{ $po->po_number }}
                            </a>
                            <small class="text-muted d-block">
                                {{ $po->invoice_date->format('d M, Y') }} &bull; {{ $po->user->username }}
                            </small>
                        </div>
                        <div class="text-end">
                            @php
                                $c =
                                    [
                                        'approved' => 'success',
                                        'submitted' => 'warning',
                                        'draft' => 'secondary',
                                        'rejected' => 'danger',
                                    ][$po->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $c }} mb-1">{{ ucfirst($po->status) }}</span>
                            <div class="small">
                                <span class="text-warning">₹{{ number_format($po->amount, 0) }}</span>
                                @if ($po->outstanding_amount > 0)
                                    <span class="text-danger ms-1">(₹{{ number_format($po->outstanding_amount, 0) }}
                                        due)</span>
                                @else
                                    <span class="text-success ms-1">✓ Cleared</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-3">No purchase orders yet.</p>
                @endforelse

            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // ── Document upload via click on button ──────────────
            $(document).on('click', '.upload-doc-btn', function() {
                const type = $(this).data('type');
                const customerId = $(this).data('customer');
                $(`#file-${type}`).trigger('click');
            });

            // ── When file is chosen, upload via AJAX ─────────────
            $(document).on('change', '.doc-file-input', function() {
                const type = $(this).data('type');
                const customerId = $(this).data('customer');
                const file = this.files[0];

                if (!file) return;

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('customer_id', customerId);
                formData.append('type', type);
                formData.append('file', file);

                const btn = $(`.upload-doc-btn[data-type="${type}"]`);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                $.ajax({
                    url: '{{ route('school-documents.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // Reload page to show updated document state
                        location.reload();
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message ?? 'Upload failed. Please try again.');
                        btn.prop('disabled', false).html(
                            '<i class="feather icon-upload me-1"></i> Upload');
                    }
                });
            });

        });
    </script>
@endpush
