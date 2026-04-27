@csrf
<div class="row">

    <input type="hidden" name="type" value="{{ isset($bill) ? $bill->type : $type }}">

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Bill No.</label>
        <div class="form-control">{{ $bill->bill_number ?? $bill_number }}</div>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Bill Date</label>
        <input type="date" class="form-control" name="bill_date"
            value="{{ isset($bill) ? $bill->bill_date->format('Y-m-d') : date('Y-m-d') }}" required>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Customer Name</label>
        <select name="customer_id" id="customer_id" class="form-control" required>
            @if(isset($bill))
            @foreach($customers as $key => $customer)
            <option value="{{ $key }}" {{ $bill->customer_id == $key ? 'selected' : '' }}>
                {{ $customer }}
            </option>
            @endforeach
            @else
            <option selected value="" disabled>Choose...</option>
            @foreach($customers as $key => $customer)
            <option {{ old('customer_id')==$key ? 'selected' : '' }} value="{{ $key }}">{{ $customer}}</option>
            @endforeach
            @endif
        </select>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Phone No.</label>
        <input type="text" class="form-control" name="phone_number" value="{{ $bill->phone_number ?? old('phone_number') }}">
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">GST No.</label>
        <input type="text" class="form-control" name="gst_number" value="{{ $bill->gst_number ?? old('gst_number') }}">
    </div>



    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Address</label>
        <input type="text" class="form-control" name="address" value="{{ $bill->address ?? old('address') }}">
    </div>


</div>


@include('bills.items')

<footer class="d-flex justify-content-between mt-3 mt-lg-0 mb-4">
    <button class="btn btn-primary btn-sm" id="add-row">
        <span class="feather icon-plus"></span>
    </button>
    <button class="btn btn-danger btn-sm" id="remove-row">
        <i class="feather icon-x"></i>
    </button>
</footer>


<div class="form-check mb-4">
    <input class="form-check-input" type="checkbox" id="add-transport" {{ isset($bill->transport_id) ? 'checked' : '' }}>
    <label class="form-check-label" for="add-transport">Add Transport</label>
</div>

{{-- transport --}}
<section class="row {{ isset($bill->transport_id) ? '' : 'd-none' }}" id="transport">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Transport Name</label>
        <select name="transport_id" id="transport_id" class="form-control">
            <option selected value="" disabled>Choose...</option>
            @if(isset($bill))
            @foreach($transports as $key => $transport)
            <option value="{{ $key }}" {{ $bill->transport_id == $key ? 'selected' : '' }}>
                {{ $transport }}
            </option>
            @endforeach
            @else
            @foreach($transports as $key => $transport)
            <option {{ old('transport_id')== $key ? 'selected' : '' }} value="{{ $key }}">{{ $transport}}</option>
            @endforeach
            @endif
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Vehicle No.</label>
        <input type="text" class="form-control" name="vehicle_number" value="{{ $bill->vehicle_number ?? old('vehicle_number') }}">
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Bilty No.</label>
        <input type="text" class="form-control" name="bilty_number" value="{{ $bill->bilty_number ?? old('bilty_number') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Delivery Date</label>
        <input type="date" class="form-control" name="delivery_date" value="{{ isset($bill->delivery_date) ? $bill->delivery_date->format('Y-m-d') : old('delivery_date') }}">
    </div>

</section>


<div class="mb-3">
    <label for="" class="form-label">Remarks</label>
    <textarea name="remarks" cols="30" rows="3"  class="form-control">{{ $bill->remarks ?? old('remarks') }}</textarea>
</div>


<div class="mb-3">
    <label for="" class="form-label">Terms & Conditions</label>
    <textarea name="terms" cols="30" rows="3"  class="form-control">{{ $bill->terms ?? old('terms') }}</textarea>
</div>


<div id="filepond-alert" class="alert alert-danger d-none my-3">
    Only images, pdf, doc, excel files are allowed with max size of 10MB.
</div>

<div class="mb-3">
    <label for="attachemnts" class="form-label">Attachments</label>
    <input type="file" name="attachments[]" multiple max="3" id="attachemnts">
</div>

<div class="mb-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="is_approved"
        value="1" {{ isset($bill) ? $bill->is_approved ? 'checked' : '' : '' }}>
        <label class="form-check-label" for="is_approved">Approved</label>
    </div>
</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Update' }}</button>


@push('scripts')

<script>

    function enableSelectize(){
       $('table#item tbody').find('select').selectize({ sortField: 'text' });
    }

    $(document).ready(() => {


        $('select').selectize();


        $('table#item tbody').on('input',`input[name='quantities[]']`,function(){

            let rateEl = $(this).parent().parent().find(`input[name='rates[]']`);

            rateEl.val() ?  rateEl.val() : rateEl.val(0);

            let total = $(this).val() * rateEl.val();

            $(this).parent().parent().find(`input[name='amounts[]']`).val(total.toFixed(2));

            setAmount();
            setTotalAmount();

        });


        $('table#item tbody').on('input',`input[name='rates[]']`,function(){

            let quantityEl = $(this).parent().parent().find(`input[name='quantities[]']`);

            quantityEl.val() ?  quantityEl.val() : quantityEl.val(0);

            let total = $(this).val() * quantityEl.val();

            $(this).parent().parent().find(`input[name='amounts[]']`).val(total.toFixed(2));

            setAmount();
            setTotalAmount();

        });


        $('#add-row').click(function(e){

            e.preventDefault();

           $('table#item tbody tr:last').find('select').each(function (el) {
               let value = $(this).val();
               $(this)[0].selectize.destroy();
               $(this).val(value);
           });

           $('table#item tbody tr:last').clone().appendTo('table#item tbody').find('[name]').val('');

            enableSelectize();
       });


        // remove last row
        $('#remove-row').on('click', (e)=>{
           e.preventDefault();
           $('table#item tbody tr:last').remove();
       });


        function setAmount(){

            let amount = 0;

            $(`input[name='amounts[]']`).each(function(index, el){
                amount = amount + parseFloat($(this).val());
            });
            console.log(amount);
            $('[name=amount]').val(amount.toFixed(2));
        }

        $('input#cgst-percentage').on('input', function(){

            let amount = parseFloat($('[name=amount]').val()).toFixed(2);
            let cgst_amount = amount / 100 * parseInt($(this).val());
            $('[name=cgst_amount]').val(cgst_amount.toFixed(2));
            setTotalAmount();

        });

        $('input#sgst-percentage').on('input', function(){

            let amount = parseFloat($('[name=amount]').val()).toFixed(2);
            let sgst_amount = amount / 100 * parseInt($(this).val());
            $('[name=sgst_amount]').val(sgst_amount.toFixed(2));
            setTotalAmount();

        });

        $('input#igst-percentage').on('input', function(){

            let amount = parseFloat($('[name=amount]').val()).toFixed(2);
            let igst_amount = amount / 100 * parseInt($(this).val());
            $('[name=igst_amount]').val(igst_amount.toFixed(2));
            setTotalAmount();

        });


        $('input#transport_charges').on('input', setTotalAmount);
        $('input#extra_charges').on('input', setTotalAmount);


        function setTotalAmount(){

            let totalAmount = parseFloat($('[name=amount]').val())
                    + parseFloat($('[name=cgst_amount]').val())
                    + parseFloat($('[name=sgst_amount]').val())
                    + parseFloat($('[name=igst_amount]').val())
                    + parseFloat($('[name=transport_charges]').val())
                    + parseFloat($('[name=extra_charges]').val());

            $('[name=total_amount]').val(totalAmount.toFixed(2));

        }


        const filePondAlertEl = $('#filepond-alert');

        FilePond.create(document.querySelector('#attachemnts'));

        FilePond.setOptions({
            server : {
                headers : {
                      'X-CSRF_TOKEN' : '{{ csrf_token() }}',
                      'X-Requested-With': 'XMLHttpRequest',
                },
                process : {
                    url : `{{ route('bills.attachments.store') }}`,
                    onerror : (res) => {
                        console.log(res);
                        filePondAlertEl.removeClass('d-none');
                        setTimeout(() => filePondAlertEl.addClass('d-none'), 4000);
                    }
                },
                revert: {
                  url : `{{ route('bills.attachments.destroy') }}`,
                  _method : 'DELETE',
                }
            }
        });


       tinymce.init({
            selector: '[name=terms]',
            height: 420,
            branding: false,
            plugins: 'lists link image paste table fullscreen',
            toolbar: `undo redo | bold italic underline | alignleft
                    aligncenter alignright alignjustify | bullist numlist outdent indent
                    | table |link image | fullscreen`,
        });


        $('select[name=customer_id]').change(function(){

            const url = `{{ route('customers.index') }}`;
            let customer_id = $(this).val();

            $.ajax({
                url : `${url}?customer_id=${customer_id}`
            }).done((res)=>{

                $('[name=phone_number]').val(res.phone_number);
                $('[name=gst_number]').val(res.gst_number);
                $('[name=address]').val(res.address);

            }).fail(error => console.log(error));
        });


        $('#add-transport[type=checkbox]').click(function(){

            $(this).is(':checked')
                ? $('section#transport').removeClass('d-none')
                : $('section#transport').addClass('d-none');
        });


    });

</script>

@endpush
