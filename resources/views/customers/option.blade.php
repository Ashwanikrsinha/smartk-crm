<option value="{{ $new_customer->id }}" selected>{{ $new_customer->name }}</option>
@foreach($customers as $key => $customer)
<option value="{{ $key }}">{{ $customer }}</option>
@endforeach