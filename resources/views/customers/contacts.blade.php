<section class="table-responsive-lg rounded mt-4">
    <table class="table table-bordered" id="contact" style="min-width: 50rem;">
        <thead>
            <tr>
                <th colspan="5" class="text-center">
                    <i class="feather icon-users me-1"></i> Contact Persons
                </th>
            </tr>
            <tr>
                <th>Person Name</th>
                <th>Birth Date</th>
                <th>Anniversary Date</th>
                <th>Contact No.</th>
                <th>Designation</th>
            </tr>
        </thead>
        <tbody>
           @if (isset($customer) && $customer->contacts->count() > 0)
            @foreach ($customer->contacts as $contact)
            <tr>
                <td>
                    <input type="text" name="person_name[]" value="{{ $contact->name }}" class="form-control">
                </td>
                <td>
                    <input type="date" name="birth_date[]"
                        value="{{ isset($contact->birth_date) ? $contact->birth_date->format('Y-m-d') : '' }}"
                        class="form-control">
                </td>
                <td>
                    <input type="date" name="marriage_date[]"
                        value="{{ isset($contact->marriage_date) ? $contact->marriage_date->format('Y-m-d') : ''  }}"
                        class="form-control">
                </td>
                <td>
                    <input type="text" name="contact_number[]" value="{{ $contact->contact_number }}"
                        class="form-control">
                </td>
                <td style="min-width: 12rem;">
                    <select name="designation_id[]" id="" class="form-control">
                        <option value="" selected disabled>Choose...</option>
                        @foreach ($designations as $id => $designation)
                        <option {{ $contact->designation_id == $id ? 'selected' : '' }} value="{{ $id }}">
                        {{ $designation }}
                        </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            @endforeach
           @else
            <tr>
                <td>
                    <input type="text" name="person_name[]" class="form-control">
                </td>
                <td>
                    <input type="date" name="birth_date[]" class="form-control">
                </td>
                <td>
                    <input type="date" name="marriage_date[]" class="form-control">
                </td>
                <td>
                    <input type="text" name="contact_number[]" class="form-control">
                </td>
                <td style="min-width: 12rem;">
                    <select name="designation_id[]" id="" class="form-control">
                        <option value="" selected>Choose...</option>
                        @foreach ($designations as $id => $designation)
                        <option value="{{ $id }}">{{ $designation}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
           @endif
        </tbody>
    </table>
</section>

<footer class="d-flex justify-content-between mt-3 mt-lg-0 mb-4">
    <button class="btn btn-sm btn-primary" id="add-row">
        <span class="feather icon-plus"></span>
    </button>
    <button class="btn btn-sm btn-danger" id="remove-row">
        <i class="feather icon-x"></i>
    </button>
</footer>
