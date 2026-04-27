<?php $__env->startSection('content'); ?>

    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Reports</h5>
            <small class="text-muted">Filter and export purchase order data</small>
        </div>
        <a href="<?php echo e(route('reports.export', request()->all())); ?>" class="btn btn-success btn-sm">
            <i class="feather icon-download me-1"></i> Export Excel
        </a>
    </header>


    
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="<?php echo e(route('reports.index')); ?>" id="filter-form">

            <div class="row g-2 mb-2">

                
                <?php if(!auth()->user()->isSalesPerson()): ?>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small mb-1">Sales Person</label>
                        <select name="sp_id" class="form-control form-control-sm">
                            <option value="">All SPs</option>
                            <?php $__currentLoopData = $teamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sp->id); ?>" <?php echo e($spId == $sp->id ? 'selected' : ''); ?>>
                                    <?php echo e($sp->username); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>

                
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small mb-1">School</label>
                    <select name="school_id" class="form-control form-control-sm">
                        <option value="">All Schools</option>
                        <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php echo e($schoolId == $s->id ? 'selected' : ''); ?>>
                                [<?php echo e($s->school_code); ?>] <?php echo e($s->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">Lead From</label>
                    <select name="lead_source_id" class="form-control form-control-sm">
                        <option value="">All Sources</option>
                        <?php $__currentLoopData = $leadSources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ls->id); ?>" <?php echo e($leadSrcId == $ls->id ? 'selected' : ''); ?>>
                                <?php echo e($ls->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">State</label>
                    <input type="text" name="state" class="form-control form-control-sm" list="state-list"
                        value="<?php echo e($state ?? ''); ?>" placeholder="State...">
                    <datalist id="state-list">
                        <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st); ?>">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </datalist>
                </div>

                
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All Statuses</option>
                        <?php $__currentLoopData = ['draft', 'submitted', 'approved', 'rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s); ?>" <?php echo e($status === $s ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst($s)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

            </div>

            <div class="row g-2 align-items-end">

                
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small mb-1">Month</label>
                    <input type="month" name="month" class="form-control form-control-sm" value="<?php echo e($month ?? ''); ?>"
                        id="month-filter">
                </div>

                
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                        value="<?php echo e($dateFrom ?? ''); ?>" id="date-from">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo e($dateTo ?? ''); ?>"
                        id="date-to">
                </div>

                
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">Year</label>
                    <select name="year" class="form-control form-control-sm">
                        <?php for($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?>

                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="feather icon-filter me-1"></i> Apply
                    </button>
                    <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="feather icon-x"></i>
                    </a>
                </div>

            </div>

        </form>
    </div>


    
    <div class="row g-3 mb-4">
        <?php
            $widgets = [
                ['label' => 'A: Total PO Amount', 'value' => $totals['po_amount'], 'color' => 'warning'],
                ['label' => 'B: Total Billed', 'value' => $totals['billing_amount'], 'color' => 'info'],
                ['label' => 'C: Pending PO (A−B)', 'value' => $totals['pending_po'], 'color' => 'secondary'],
                ['label' => 'D: Total Collected', 'value' => $totals['collected'], 'color' => 'success'],
                ['label' => 'E: Outstanding (B−D)', 'value' => $totals['outstanding'], 'color' => 'danger'],
            ];
        ?>

        <?php $__currentLoopData = $widgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-lg">
                <div class="bg-white rounded shadow-sm p-3 text-center border-top border-<?php echo e($w['color']); ?> border-3">
                    <h5 class="fw-bold text-<?php echo e($w['color']); ?> mb-1">₹<?php echo e(number_format($w['value'], 0)); ?></h5>
                    <small class="text-muted"><?php echo e($w['label']); ?></small>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>


    
    <div class="bg-white rounded shadow-sm p-3">

        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h6 class="fw-bold mb-0">
                <i class="feather icon-list me-2 text-primary"></i>
                Records
                <span class="badge bg-secondary ms-1"><?php echo e($rows->count()); ?></span>
            </h6>
            <a href="<?php echo e(route('reports.export', request()->all())); ?>" class="btn btn-sm btn-outline-success">
                <i class="feather icon-download me-1"></i> Export Excel
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover" id="reports-table">
                <thead class="table-light">
                    <tr>
                        <th>PO Number</th>
                        <th>Date</th>
                        <?php if(!auth()->user()->isSalesPerson()): ?>
                            <th>SP Name</th>
                        <?php endif; ?>
                        <th>School</th>
                        <th>State</th>
                        <th>Lead From</th>
                        <th class="text-end text-warning">A: PO Amt</th>
                        <th class="text-end text-info">B: Billed</th>
                        <th class="text-end text-secondary">C: Pend. PO</th>
                        <th class="text-end text-success">D: Collected</th>
                        <th class="text-end text-danger">E: Outstanding</th>
                        <th>Status</th>
                        <th>Delivery Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('invoices.show', $row->id)); ?>" class="text-primary">
                                    <?php echo e($row->po_number); ?>

                                </a>
                            </td>
                            <td><?php echo e($row->invoice_date->format('d M, Y')); ?></td>
                            <?php if(!auth()->user()->isSalesPerson()): ?>
                                <td><?php echo e($row->user->username); ?></td>
                            <?php endif; ?>
                            <td>
                                <?php echo e($row->customer->name); ?>

                                <small class="text-muted d-block"><?php echo e($row->customer->school_code); ?></small>
                            </td>
                            <td><?php echo e($row->customer->state); ?></td>
                            <td><?php echo e($row->customer->leadSource?->name ?? '—'); ?></td>
                            <td class="text-end">₹<?php echo e(number_format($row->amount, 2)); ?></td>
                            <td class="text-end">₹<?php echo e(number_format($row->billing_amount, 2)); ?></td>
                            <td class="text-end">₹<?php echo e(number_format($row->amount - $row->billing_amount, 2)); ?></td>
                            <td class="text-end">₹<?php echo e(number_format($row->collected_amount, 2)); ?></td>
                            <td
                                class="text-end <?php echo e($row->outstanding_amount > 0 ? 'text-danger fw-bold' : 'text-success'); ?>">
                                ₹<?php echo e(number_format($row->outstanding_amount, 2)); ?>

                            </td>
                            <td>
                                <?php
                                    $map = [
                                        'approved' => 'success',
                                        'submitted' => 'warning',
                                        'draft' => 'secondary',
                                        'rejected' => 'danger',
                                    ];
                                    $c = $map[$row->status] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo e($c); ?>"><?php echo e(ucfirst($row->status)); ?></span>
                            </td>
                            <td><?php echo e($row->delivery_due_date ? $row->delivery_due_date->format('d M, Y') : '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">
                                No records found for the selected filters.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

                <?php if($rows->count()): ?>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="<?php echo e(auth()->user()->isSalesPerson() ? 5 : 6); ?>">Total (<?php echo e($rows->count()); ?>

                                records)</td>
                            <td class="text-end">₹<?php echo e(number_format($totals['po_amount'], 2)); ?></td>
                            <td class="text-end">₹<?php echo e(number_format($totals['billing_amount'], 2)); ?></td>
                            <td class="text-end">₹<?php echo e(number_format($totals['pending_po'], 2)); ?></td>
                            <td class="text-end">₹<?php echo e(number_format($totals['collected'], 2)); ?></td>
                            <td class="text-end">₹<?php echo e(number_format($totals['outstanding'], 2)); ?></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>

            </table>
        </div>

    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            $('select').selectize();

            // If month is selected, clear date range and vice versa
            $('#month-filter').on('change', function() {
                if ($(this).val()) {
                    $('#date-from, #date-to').val('');
                }
            });
            $('#date-from, #date-to').on('change', function() {
                if ($(this).val()) {
                    $('#month-filter').val('');
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/reports/index.blade.php ENDPATH**/ ?>