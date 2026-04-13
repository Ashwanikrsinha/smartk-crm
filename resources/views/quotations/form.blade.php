@csrf
<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Quotation Date</label>
        <input type="date" class="form-control" name="quotation_date"
            value="{{ isset($quotation) ? $quotation->quotation_date->format('Y-m-d') : date('Y-m-d') }}" required>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Customer Name</label>
        <select name="customer_id" id="customer_id" class="form-control" required>
            <option selected value="">Choose...</option>
            @if(isset($quotation))
            @foreach($customers as $key => $customer)
            <option value="{{ $key }}" {{ request('customer_id')==$key ? 'selected' : '' }} {{ !request('customer_id') &&
                $quotation->customer_id == $key ? 'selected' : '' }}>
                {{ $customer }}
            </option>
            @endforeach
            @else
            @foreach($customers as $key => $customer)
            <option {{ request('customer_id') == $key || old('customer_id')==$key ? 'selected' : '' }} value="{{ $key }}">{{
                $customer }}</option>
            @endforeach
            @endif
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Visit No.</label>
        <select name="visit_id" id="" class="form-control" required>
           <option value="" selected disabled>Choose...</option>
           @if (isset($quotation) && blank($visits)))
              <option value="{{ $quotation->visit->id }}" selected>V-{{ $quotation->visit->visit_number }}</option>
           @elseif(isset($visits))
                @foreach($visits as $key => $visit_number)
                <option value="{{ $key }}" {{ request('visit_id') == $key ? 'selected' : '' }}>
                    V-{{ $visit_number }}
                </option>
                @endforeach
           @endif
        </select>
    </div>


    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Assign To (User)</label>
        <select name="user_id" id="user_id" class="form-control" required>
            @if(isset($quotation))
            @foreach($users as $key => $user)
            <option value="{{ $key }}" {{ $quotation->user_id == $key ? 'selected' : '' }}>
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

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Status</label>
        <select name="status" id="status" class="form-control" required>
            <option selected value="">Choose...</option>
            @if(isset($quotation))
            @foreach($statuses as $status)
            <option value="{{ $status }}" {{ $quotation->status == $status ? 'selected' : '' }}>
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


@if(isset($visit) && $mode == 'create')
    @include('visits.items') 
@elseif (!isset($visit) && $mode == 'edit')
    @include('quotations.items')
@elseif(isset($visit) && $mode == 'edit')
    @include('quotations.items')
@endif


<section class="row {{ isset($quotation) && $quotation->status == App\Models\Visit::FOLLOW_UP ? '' : 'd-none' }}" id="follow-up">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Follow Up Date</label>
        <input type="date" class="form-control" name="follow_date" value="{{ isset($quotation->follow_date) ? $quotation->follow_date->format('Y-m-d') : old('follow_date') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Follow Up Type</label>
        <select name="follow_type" id="follow_type" class="form-control">
            <option selected value="" disabled>Choose...</option>
            @if(isset($quotation))
            @foreach($follow_types as $type)
            <option value="{{ $type }}" {{ $quotation->follow_type == $type ? 'selected' : '' }}>
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


<div class="{{ isset($quotation) && $quotation->status == App\Models\Quotation::NOT_INTERESTED ? '' : 'd-none' }} mb-3" id="reason">
    <label for="" class="form-label">Reason</label>
    <textarea name="reason" cols="30" rows="3" class="form-control">{{ $quotation->reason  ?? old('reason') }}</textarea>
</div>


<div class="mb-3">
    <label for="" class="form-label">Remarks</label>
    <textarea name="remarks" cols="30" rows="3" class="form-control">{{ $quotation->remarks ?? old('remarks') }}</textarea>
</div>


<div class="mb-3">
    <label for="attachemnts" class="form-label">Attachments</label>
    <input type="file" name="attachments[]" multiple max="3" id="attachemnts" class="shadow-sm">
    <small class="text-muted d-block" style="transform: translateY(-6px);">
        <strong>Formats</strong>: images, pdf, excel, docx.</small>
</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Edit' }}</button>


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


        $('select[name=customer_id]').on('change', function(){
            if ($(this).val().length > 0) {
              window.location = `${window.location.origin}${window.location.pathname}?customer_id=${$(this).val()}`;   
            }
        }); 


        $('select[name=visit_id]').on('change', function(){
            if ($(this).val().length > 0) {
               window.location = `${window.location.origin}${window.location.pathname}?customer_id=${$('[name=customer_id]').val()}&visit_id=${$(this).val()}`;
            }
        }); 


        
        FilePond.create(document.querySelector('#attachemnts'));

        FilePond.setOptions({
            server : {
                process : {  
                    url : `{{ route('quotations.attachments.store') }}`,
                    headers : { 'X-CSRF_TOKEN' : '{{ csrf_token() }}' },
                    onerror : (json) => { console.log(JSON.parse(json)); }
                },
                revert: {
                  url : `{{ route('quotations.attachments.destroy') }}`,
                  _method : 'DELETE',
                  headers : { 'X-CSRF_TOKEN' : '{{ csrf_token() }}' }
                }
            }
        });


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

            const followUp = '{{ App\Models\Quotation::FOLLOW_UP }}';
            const notInterested = '{{ App\Models\Quotation::NOT_INTERESTED }}';

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