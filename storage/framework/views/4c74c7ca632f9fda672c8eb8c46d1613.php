<?php $__env->startSection('title', 'SOP Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>SOP Reports</h1>
        <div class="breadcrumb">📊 Store SOP Scores &bull; Overall Performance</div>
    </div>
</div>

<!-- Summary Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($stores->count()); ?></span>
            <span class="stat-label">Active Stores</span>
        </div>
        <div class="stat-icon blue">🏪</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($stores->sum('sop_checklists_count')); ?></span>
            <span class="stat-label">Total Audits</span>
        </div>
        <div class="stat-icon green">📋</div>
    </div>
    <div class="stat-card">
        <div>
            <?php
                $overallAvg = $stores->where('sop_checklists_avg_overall_value', '>', 0)->avg('sop_checklists_avg_overall_value');
            ?>
            <span class="stat-value"><?php echo e($overallAvg ? number_format($overallAvg, 1) : '-'); ?></span>
            <span class="stat-label">Average SOP Score</span>
        </div>
        <div class="stat-icon <?php echo e($overallAvg >= 7 ? 'green' : ($overallAvg >= 5 ? 'yellow' : 'red')); ?>">⭐</div>
    </div>
</div>

<!-- Store SOP Table -->
<div class="card">
    <div class="card-header">
        <h3>📊 Store SOP Scores</h3>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Store</th>
                    <th>Address</th>
                    <th>Audits</th>
                    <th>Avg Score</th>
                    <th>Rating</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="font-weight:600;"><?php echo e($store->name); ?></td>
                    <td class="text-sm text-muted"><?php echo e(Str::limit($store->address, 40)); ?></td>
                    <td>
                        <span class="badge badge-info"><?php echo e($store->sop_checklists_count); ?></span>
                    </td>
                    <td>
                        <?php if($store->sop_checklists_avg_overall_value): ?>
                            <span style="font-weight:700;font-size:18px;color:<?php echo e($store->sop_checklists_avg_overall_value >= 7 ? 'var(--success)' : ($store->sop_checklists_avg_overall_value >= 5 ? 'var(--warning)' : 'var(--danger)')); ?>;">
                                <?php echo e(number_format($store->sop_checklists_avg_overall_value, 1)); ?>

                            </span>
                            <span class="text-muted text-sm">/ 10</span>
                        <?php else: ?>
                            <span class="text-muted">No data</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($store->sop_checklists_avg_overall_value): ?>
                            <?php $score = $store->sop_checklists_avg_overall_value; ?>
                            <div style="width:80px;height:8px;background:var(--bg-input);border-radius:4px;overflow:hidden;">
                                <div style="width:<?php echo e(($score / 10) * 100); ?>%;height:100%;background:<?php echo e($score >= 7 ? 'var(--success)' : ($score >= 5 ? 'var(--warning)' : 'var(--danger)')); ?>;border-radius:4px;"></div>
                            </div>
                        <?php else: ?>
                            <span class="text-muted text-sm">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/admin/sop-reports/<?php echo e($store->id); ?>" class="btn btn-sm btn-outline">View Details</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-muted text-sm" style="text-align:center;padding:24px;">No stores found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/sop-reports/index.blade.php ENDPATH**/ ?>