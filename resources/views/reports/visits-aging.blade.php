@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Visits Aging - Report</h5>
  <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

@if(isset($visits))
<section class="table-responsive rounded shadow-sm rounded bg-white mb-4">
  <table class="table">
    <thead>
      <tr>
        <th class="ps-3">Visit Date</th>
        <th>Customer Name</th>
        <th>No. of Days From Last Visit</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($visits as $visit)
      <tr>
        <td class="ps-3">{{ $visit->visit_date->format('d M, Y') }}</td>
        <td>{{ $visit->customer->name }}</td>
        <td>{{ $visit->total_days }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="d-flex justify-content-end">
    {{ $visits->links() }}
  </div>
</section>
@endisset


@endsection