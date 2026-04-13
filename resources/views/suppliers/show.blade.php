@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <h5>Supplier</h5>
    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-users me-1 bg-primary text-white rounded p-1"></i> Supplier
    </header>
    <div class="card-body p-0">
        <section class="table-responsive mb-4">
            <table class="table" style="min-width: 60rem;">
                <tbody>
                    <tr>
                        <th class="ps-3">Name</th>
                        <td>{{ $supplier->name }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Supplier Type</th>
                        <td>
                            @foreach ($supplier->types() as $type)
                            <span class="badge alert-primary">{{ $type->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th class="ps-3">Segment</th>
                        <td>{{ $supplier->segment->name }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Phone No.</th>
                        <td>{{ $supplier->phone }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Email</th>
                        <td>{{ $supplier->email }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Address</th>
                        <td>{{ $supplier->address }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">State</th>
                        <td>{{ $supplier->state }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">City</th>
                        <td>{{ $supplier->city }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
</div>


<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-book me-1 bg-primary text-white rounded p-1"></i> Description
    </header>
    <div class="card-body">
        {{ $supplier->description ?? 'NOT GIVEN' }}
    </div>
</div>

@if($supplier->contacts->count() > 0)
<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-users me-1 bg-primary text-white rounded p-1"></i> Contacts
    </header>
    <div class="card-body p-0">
        <section class="table-responsive rounded mb-4">
            <table class="table" id="contact" style="min-width: 50rem;">
                <thead>
                    <tr>
                        <th class="ps-3">Person Name</th>
                        <th>Birth Date</th>
                        <th>Anniversary Date</th>
                        <th>Contact No.</th>
                        <th>Designation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($supplier->contacts as $contact)
                    <tr>
                        <td class="ps-3">{{ $contact->name }}</td>
                        <td>{{ isset($contact->birth_date) ? $contact->birth_date->format('d M, Y') : 'NOT GIVEN' }}</td>
                        <td>{{ isset($contact->marriage_date) ? $contact->marriage_date->format('d M, Y') : 'NOT GIVEN' }}</td>
                        <td>{{ $contact->contact_number }}</td>
                        <td>{{ isset($contact->designation_id) ? $contact->designation->name : 'NOT GIVEN' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
</div>
@endif

@if(isset($visits) && $visits->count() > 0)
<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-calendar me-1 bg-primary text-white rounded p-1"></i> Visits
    </header>
    <div class="card-body p-0">
        <section class="table-responsive rounded">
            <table class="table" id="visits" style="min-width: 50rem;">
                <thead>
                    <tr>
                        <th class="ps-3">Visit Date</th>
                        <th>Username</th>
                        <th>Purpose</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visits as $visit)
                    <tr>
                        <td class="ps-3">{{ $visit->visit_date->format('d M, Y') }}</td>
                        <td>{{ $visit->user->username }}</td>
                        <td>{{ $visit->purpose->name }}</td>
                        <td>
                            <button type="button" class="btn btn-sm" data-bs-toggle="tooltip"
                             data-bs-placement="top"
                             data-bs-html="true"
                             title="{{ $visit->description ?? 'NOT GIVEN' }}">
                                <i class="feather icon-info text-primary"></i>
                              </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $visits->links() }}
        </section>
    </div>
</div>
@endisset

@endsection

@push('scripts')
    
<script>

   $(document).ready(()=>{

        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
        });

   });

</script>

@endpush