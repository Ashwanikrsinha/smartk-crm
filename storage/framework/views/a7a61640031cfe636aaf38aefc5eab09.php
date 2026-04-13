
<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <i class="feather icon-sliders me-1 bg-primary text-white rounded p-1"></i> Visits Summary
            </div>
            <div class="col-lg-5">
                <div class="input-group mt-3 mt-lg-0">
                    <span class="input-group-text bg-white">
                        <i class=" feather icon-tag"></i>
                    </span>
                    <select name="year" id="year" class="form-control" required>
                        <?php $__currentLoopData = range(1950, date('Y')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($year); ?>" <?php echo e($year == date('Y') ? 'selected' : ''); ?>>
                            <?php echo e($year); ?>

                          </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select name="purpose_id" id="purpose_id" class="form-control">
                        <option selected value="">Choose...</option>
                        <?php $__currentLoopData = $purposes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $purpose): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($purpose); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div> 
     </div>
    <div class="card-body">
        <canvas id="visits-chart"></canvas>
    </div>
 </div>   
 
 <?php $__env->startPush('scripts'); ?>
 <script>

    
    $('select').selectize();
    let purposeEl = $('[name=purpose_id]');
    let yearEl = $('[name=year]');


    const visitChart = new Chart('visits-chart', {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: '      Total Visits',
                data: [],
                backgroundColor: ['rgb(154 208 245)', 'rgb(248 215 218)', 'rgb(210 244 234)', 'rgb(255 243 205)'],
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            } 
            
        }
    });

    let updateChart = function() {
    
        $.ajax({
        url: `<?php echo e(route('visits.chart')); ?>?year=${ yearEl.val() }&purpose_id=${ purposeEl.val() }`,
        type: 'GET',
        dataType: 'json',
        headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
            success: function(data) {
                visitChart.data.labels = data.months;
                visitChart.data.datasets[0].data = data.visits_count;
                visitChart.update();
            },
            error: function(data){console.log(data); }
        });
    }

    updateChart();
    purposeEl.on('change', updateChart);
    yearEl.on('change', updateChart);

 </script>
 <?php $__env->stopPush(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/widgets/visit-chart.blade.php ENDPATH**/ ?>