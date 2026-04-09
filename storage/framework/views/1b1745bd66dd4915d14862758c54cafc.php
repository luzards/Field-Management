<?php $__env->startSection('title', $user->name . ' - AM Detail'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1><?php echo e($user->name); ?></h1>
        <div class="breadcrumb"><a href="/admin/users">Area Managers</a> &bull; <?php echo e($user->name); ?></div>
    </div>
    <a href="/admin/users/<?php echo e($user->id); ?>/edit" class="btn btn-outline">Edit</a>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($user->email); ?></span>
            <span class="stat-label">Email</span>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($user->phone ?? '-'); ?></span>
            <span class="stat-label">Phone</span>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($user->schedules_count); ?></span>
            <span class="stat-label">Total Schedules</span>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($user->check_ins_count); ?></span>
            <span class="stat-label">Total Check-ins</span>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><h3>📅 Recent Schedules</h3></div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Date</th><th>Store</th><th>Time</th><th>Status</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($schedule->scheduled_date->format('d M Y')); ?></td>
                        <td><?php echo e($schedule->store->name); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($schedule->start_time)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($schedule->end_time)->format('H:i')); ?></td>
                        <td>
                            <span class="badge badge-<?php echo e($schedule->status === 'completed' ? 'success' : ($schedule->status === 'missed' ? 'danger' : 'info')); ?>">
                                <?php echo e(ucfirst($schedule->status)); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="text-muted" style="text-align:center;padding:16px;">No schedules</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>📍 Recent Check-ins</h3></div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Store</th><th>Verified</th><th>Distance</th><th>Time</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentCheckIns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $checkIn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($checkIn->store->name); ?></td>
                        <td>
                            <?php if($checkIn->is_verified): ?>
                                <span class="badge badge-success">✓ Verified</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ Unverified</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($checkIn->distance_from_store); ?>m</td>
                        <td class="text-sm text-muted"><?php echo e($checkIn->checked_in_at->format('d M H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="text-muted" style="text-align:center;padding:16px;">No check-ins</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/users/show.blade.php ENDPATH**/ ?>