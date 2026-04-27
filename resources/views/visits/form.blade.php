@csrf
<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Visit Date</label>
        <input type="date" class="form-control" name="visit_date"
            value="{{ isset($visit) ? $visit->visit_date->format('Y-m-d') : date('Y-m-d') }}" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label d-flex align-items-center justify-content-between">
            Customer
            <span class="bg-primary text-white px-1 rounded" role="button" id="add-customer">&plus;</i>
        </label>
        <select name="customer_id" id="customer_id" class="form-control">
            <option selected value="">Choose...</option>
            @if(isset($visit))
            @foreach($customers as $key => $customer)
            <option value="{{ $key }}" {{ $visit->customer_id == $key ? 'selected' : '' }}>
                {{ $customer }}
            </option>
            @endforeach
            @else
            @foreach($customers as $key => $customer)
            <option {{ old('customer_id') == $key ? 'selected' : '' }} value="{{ $key }}">{{ $customer }}</option>
            @endforeach
            @endif
        </select>
    </div>

@if(!auth()->user()->isSalesPerson())
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Assign To (User)</label>
        <select name="user_id" id="user_id" class="form-control" required>
            @if(isset($visit))
            @foreach($users as $key => $user)
            <option value="{{ $key }}" {{ $visit->user_id == $key ? 'selected' : '' }}>
                {{ $user }}
            </option>
            @endforeach
            @else
            <option selected value="">Choose...</option>
            @foreach($users as $key => $user)
            <option {{ old('user_id')==$key ? 'selected' : '' }} value="{{ $key }}">{{ $user}}</option>
            @endforeach
            @endif
        </select>
    </div>
    @else
    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
    @endif

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Purpose</label>
        <select name="purpose_id" id="purpose_id" class="form-control" required>
            @if(isset($visit))
            @foreach($purposes as $key => $purpose)
            <option value="{{ $key }}" {{ $visit->purpose_id == $key ? 'selected' : '' }}>
                {{ $purpose }}
            </option>
            @endforeach
            @else
            <option selected value="">Choose...</option>
            @foreach($purposes as $key => $purpose)
            <option {{ old('purpose_id')==$key ? 'selected' : '' }} value="{{ $key }}">{{ $purpose }}</option>
            @endforeach
            @endif
        </select>
    </div>



    @include('visits.items')


    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Order Estimate</label>
        <input type="text" class="form-control" name="order_amount" value="{{ $visit->order_amount ?? old('order_amount') }}">
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Level</label>
        <select name="level" id="level" class="form-control">
            <option selected value="">Choose...</option>
            @if(isset($visit))
            @foreach($levels as $level)
            <option value="{{ $level }}" {{ $visit->level == $level ? 'selected' : '' }}>
                {{ ucfirst($level) }}
            </option>
            @endforeach
            @else
            @foreach($levels as $level)
            <option {{ old('level')==$level ? 'selected' : '' }} value="{{ $level }}">
                {{ ucfirst($level) }}
            </option>
            @endforeach
            @endif
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Status</label>
        <select name="status" id="status" class="form-control" required>
            <option selected value="">Choose...</option>
            @if(isset($visit))
            @foreach($statuses as $status)
            <option value="{{ $status }}" {{ $visit->status == $status ? 'selected' : '' }}>
                {{ ucwords($status) }}
            </option>
            @endforeach
            @else
            @foreach($statuses as $status)
            <option {{ old('status')==$status ? 'selected' : '' }} value="{{ $status }}">
                {{ ucwords($status) }}
            </option>
            @endforeach
            @endif
        </select>
    </div>

</div>


<section class="row {{ isset($visit) && $visit->status == App\Models\Visit::FOLLOW_UP ? '' : 'd-none' }}" id="follow-up">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Follow Up Date</label>
        <input type="date" class="form-control" name="follow_date" value="{{ isset($visit->follow_date) ? $visit->follow_date->format('Y-m-d') : old('follow_date') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Follow Up Type</label>
        <select name="follow_type" id="follow_type" class="form-control">
            <option selected value="" disabled>Choose...</option>
            @if(isset($visit))
            @foreach($follow_types as $type)
            <option value="{{ $type }}" {{ $visit->follow_type == $type ? 'selected' : '' }}>
                {{ ucfirst($type) }}
            </option>
            @endforeach
            @else
            @foreach($follow_types as $type)
            <option {{ old('follow_type')==$type ? 'selected' : '' }} value="{{ $type }}">{{ ucfirst($type) }}
            </option>
            @endforeach
            @endif
        </select>
    </div>


</section>

<div class="{{ isset($visit) && $visit->status == App\Models\Visit::NOT_INTERESTED ? '' : 'd-none' }} mb-3" id="reason">
    <label for="" class="form-label">Reason</label>
    <textarea name="reason" cols="30" rows="3" class="form-control">{{ $visit->reason  ?? old('reason') }}</textarea>
</div>

<div class="mb-3">
    <label for="" class="form-label">Insight</label>
    <textarea name="insight" cols="30" rows="3"
        class="form-control">{{ $visit->insight ?? old('insight') }}</textarea>
</div>

<div class="mb-4">
    <label for="" class="form-label">Action To Be Taken</label>
    <textarea name="action" cols="30" rows="3" class="form-control">{{ $visit->action ?? old('action') }}</textarea>
</div>


<div class="mb-3">
    <label for="" class="form-label">Description</label>
    <textarea name="description" cols="30" rows="5" class="form-control">{{ $visit->description ?? old('description') }}</textarea>
</div>


<div id="filepond-alert" class="alert alert-danger d-none my-3">
    Only images, pdf, doc, excel files are allowed with max size 10MB.
</div>

<div class="mb-3">
    <label for="attachemnts" class="form-label">Attachments</label>
    <input type="file" name="attachments[]" multiple max="3" id="attachemnts">
    <small class="text-muted d-block" style="transform: translateY(-6px);">
        <strong>Formats</strong>: images, pdf, excel, docx.</small>
</div>


<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Update' }}</button>

@push('scripts')

<script>

    function enableSelectize(){
       $('table#item tbody').find('select').selectize({ sortField: 'text' });
    }

    $(document).ready(() => {

        $('select').selectize();

        $('input[type=date]').flatpickr({
            altInput: true,
            altFormat: 'F j, Y',
            dateFormat: 'Y-m-d',
        });



        tinymce.init({
            selector: '[name=description]',
            height: 420,
            branding: false,
            plugins: 'lists link image paste table fullscreen',
            toolbar: `undo redo | bold italic underline | alignleft
                    aligncenter alignright alignjustify | bullist numlist outdent indent
                    | table |link image | fullscreen`,
        });



        const filePondAlertEl = $('#filepond-alert');
        FilePond.create(document.querySelector('#attachemnts'));

        FilePond.setOptions({
            server : {
                headers : {
                      'X-CSRF-TOKEN' : '{{ csrf_token() }}',
                      'X-Requested-With': 'XMLHttpRequest',
                },
                process : {
                    url : `{{ route('visits.attachments.store') }}`,
                    onerror : (res) => {
                        console.log(res);
                        filePondAlertEl.removeClass('d-none');
                        setTimeout(() => filePondAlertEl.addClass('d-none'), 4000);
                    }
                },
                revert: {
                  url : `{{ route('visits.attachments.destroy') }}`,
                  _method : 'DELETE',
                }
            }
        })


      $('#add-row').click(function(e){

            e.preventDefault();

            $('table#item tbody tr:last').find('select').each(function (el) {
                let value = $(this).val();
                $(this)[0].selectize.destroy();
                $(this).val(value);
            });

            $('table#item tbody tr:last').clone()
                .appendTo('table#item tbody')
                .find('[name]').val('');

            enableSelectize();
        });


        // remove last row
        $('#remove-row').on('click', (e)=>{
            e.preventDefault();
            $('table#item tbody tr:last').remove();
        });

        $('#check-items').click(function(){

            $('table#item tbody').find('select').each(function (el) {
                let value = $(this).val();
                $(this)[0].selectize.destroy();
                $(this).val(value);
            });

            if($(this).is(':checked')){
                $('table#item tbody').find('[name]').prop('disabled', false);
                $('table#item tbody').find('[name]').prop('required', true);
                $('table#item tbody').find(`[name='descriptions[]']`).prop('required', false);
            }else{
                $('table#item tbody').find('[name]').prop('disabled', true);
                $('table#item tbody').find('[name]').prop('required', false);
            }

            enableSelectize();

        });


        $('select#status').change(function(){

            const followUp = '{{ App\Models\Visit::FOLLOW_UP }}';
            const notInterested = '{{ App\Models\Visit::NOT_INTERESTED }}';

            $('section#follow-up').addClass('d-none');
            $('div#reason').addClass('d-none');


            if($(this).val() == followUp){
                $('section#follow-up').removeClass('d-none');
            }

            if($(this).val() == notInterested){
                $('div#reason').removeClass('d-none');
            }

        });


    });

</script>

@endpush
