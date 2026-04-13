<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <i class="feather icon-sliders me-1 bg-primary text-white rounded p-1"></i> Sales Figure
            </div>
            <div class="col-lg-5">
                <div class="input-group mt-3 mt-lg-0">
                    <span class="input-group-text bg-white">
                        <i class=" feather icon-tag"></i>
                    </span>
                    <select name="sale_type" id="sale-type" class="form-control">
                        <option selected value="">Choose...</option>
                        <option value="1">Weekly</option>
                        <option value="2">Monthly</option>
                        <option value="3">Yearly</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <canvas id="sales-chart"></canvas>
    </div>
</div>

@push('scripts')
<script>
    $('select').selectize();

    let saleTypeEl = $('[name=sale_type]');


    new Chart('sales-chart', {
        type: 'bar',
        data: {
            labels : ['January', 'April', 'July','October', 'December'],
            datasets : [{
                label : '       Total Sales',
                data : [12, 24, 94, 78, 87],
                backgroundColor: ['rgb(154 208 245)', 'rgb(248 215 218)', 'rgb(210 244 234)', 'rgb(255 243 205)'],
            }]
        },
        options: {
            legend: {display: false},
        title: {
        display: true,
        text: "Sales Overview"
         }
        }
    });


</script>
@endpush