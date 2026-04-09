<?php $__env->startSection('title', 'SOP Report — ' . $store->name); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1><?php echo e($store->name); ?></h1>
        <div class="breadcrumb">
            <a href="/admin/sop-reports">SOP Reports</a> &bull; Store Detail
        </div>
    </div>
    <a href="/admin/sop-reports" class="btn btn-outline btn-sm">← Back to All Stores</a>
</div>

<!-- Store Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($totalAudits); ?></span>
            <span class="stat-label">Total Audits</span>
        </div>
        <div class="stat-icon blue">📋</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value" style="color:<?php echo e($avgScore >= 7 ? 'var(--success)' : ($avgScore >= 5 ? 'var(--warning)' : 'var(--danger)')); ?>">
                <?php echo e($avgScore ? number_format($avgScore, 1) : '-'); ?>

            </span>
            <span class="stat-label">Average SOP Score</span>
        </div>
        <div class="stat-icon <?php echo e($avgScore >= 7 ? 'green' : ($avgScore >= 5 ? 'yellow' : 'red')); ?>">⭐</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($store->address); ?></span>
            <span class="stat-label">Address</span>
        </div>
        <div class="stat-icon blue">📍</div>
    </div>
</div>

<!-- Score Distribution -->
<?php if($totalAudits > 0): ?>
<div class="card">
    <div class="card-header">
        <h3>📊 Score Distribution</h3>
    </div>
    <div style="display:flex;align-items:flex-end;gap:6px;height:100px;padding:10px 0;">
        <?php for($i = 1; $i <= 10; $i++): ?>
            <?php
                $count = $distribution[$i] ?? 0;
                $maxCount = max(array_values($distribution ?: [1]));
                $height = $maxCount > 0 ? ($count / $maxCount) * 80 : 0;
                $color = $i >= 7 ? 'var(--success)' : ($i >= 5 ? 'var(--warning)' : 'var(--danger)');
            ?>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
                <span class="text-sm" style="color:var(--text-muted);font-size:11px;"><?php echo e($count); ?></span>
                <div style="width:100%;height:<?php echo e(max($height, 4)); ?>px;background:<?php echo e($color); ?>;border-radius:4px 4px 0 0;transition:height 0.3s;"></div>
                <span class="text-sm" style="font-weight:600;font-size:12px;"><?php echo e($i); ?></span>
            </div>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>

<!-- Audit History -->
<div class="card">
    <div class="card-header">
        <h3>📝 Audit History</h3>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>AM</th>
                    <th>Score</th>
                    <th>Items</th>
                    <th>Comments</th>
                    <th>Photos</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $checklists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-sm"><?php echo e($cl->created_at->format('d M Y H:i')); ?></td>
                    <td style="font-weight:500;"><?php echo e($cl->user->name); ?></td>
                    <td>
                        <span style="font-weight:700;font-size:18px;color:<?php echo e($cl->overall_value >= 7 ? 'var(--success)' : ($cl->overall_value >= 5 ? 'var(--warning)' : 'var(--danger)')); ?>">
                            <?php echo e($cl->overall_value); ?>

                        </span>
                        <span class="text-muted text-sm">/ 10</span>
                    </td>
                    <td>
                        <?php if(is_array($cl->items)): ?>
                            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                <?php $__currentLoopData = $cl->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge <?php echo e(($item['checked'] ?? false) ? 'badge-success' : 'badge-danger'); ?>" style="font-size:11px;">
                                        <?php echo e($item['checked'] ?? false ? '✓' : '✗'); ?> <?php echo e($item['name'] ?? ''); ?>

                                        <?php if(isset($item['value'])): ?>
                                            (<?php echo e($item['value']); ?>)
                                        <?php endif; ?>
                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="text-sm" style="max-width:200px;">
                        <?php echo e($cl->comments ? Str::limit($cl->comments, 60) : '—'); ?>

                    </td>
                    <td>
                        <?php if(is_array($cl->photos) && count($cl->photos) > 0): ?>
                            <div style="display:flex;gap:4px;">
                                <?php $__currentLoopData = $cl->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="/storage/<?php echo e($photo); ?>" target="_blank">
                                        <img src="/storage/<?php echo e($photo); ?>" alt="SOP Photo" style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted text-sm">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-muted text-sm" style="text-align:center;padding:24px;">No audits found for this store</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($checklists->hasPages()): ?>
    <div class="pagination">
        <?php echo e($checklists->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/sop-reports/show.blade.php ENDPATH**/ ?>