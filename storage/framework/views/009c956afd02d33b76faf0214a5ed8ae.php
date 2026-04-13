<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <i class="feather icon-sliders me-1 bg-primary text-white rounded p-1"></i> Purchases Figure
            </div>
            <div class="col-lg-5">
                <div class="input-group mt-3 mt-lg-0">
                    <span class="input-group-text bg-white">
                        <i class=" feather icon-tag"></i>
                    </span>
                    <select name="purchase_type" id="purchase-type" class="form-control">
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
        <canvas id="purchases-chart"></canvas>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    $('select').selectize();

    let purchaseTypeEl = $('[name=purchase_type]');


    new Chart('purchases-chart', {
        type: 'line',
        data: {
            labels : ['January', 'April', 'July','October', 'December'],
            datasets : [{
                label : '      Total purchases',
                data : [12, 74, 54, 18, 12, 45, 23],
                backgroundColor: 'rgb(154 208 245)',
                borderColor : 'rgb(154 208 245)'
            }]
        },
        options: {
            legend: { display: false },
            title: { display: true, text: "purchases Overview" }
        }
    });


</script>
<?php $__env->stopPush(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/widgets/purchase-chart.blade.php ENDPATH**/ ?>