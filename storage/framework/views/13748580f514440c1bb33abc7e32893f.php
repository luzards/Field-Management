<?php $__env->startSection('title', 'Schedules'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Schedules</h1>
        <div class="breadcrumb">Management &bull; Schedules</div>
    </div>
    <a href="/admin/schedules/create" class="btn btn-primary">+ New Schedule</a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="/admin/schedules" class="flex-row">
            <select name="user_id" class="form-control" style="max-width:200px;">
                <option value="">All AMs</option>
                <?php $__currentLoopData = $ams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $am): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($am->id); ?>" <?php echo e(request('user_id') == $am->id ? 'selected' : ''); ?>><?php echo e($am->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="date" name="date" class="form-control" style="max-width:180px;" value="<?php echo e(request('date')); ?>">
            <select name="status" class="form-control" style="max-width:150px;">
                <option value="">All Status</option>
                <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                <option value="missed" <?php echo e(request('status') === 'missed' ? 'selected' : ''); ?>>Missed</option>
                <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline">Filter</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Date</th><th>AM</th><th>Store</th><th>Time</th><th>Status</th><th>Check-in</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($schedule->scheduled_date->format('d M Y')); ?></td>
                    <td><?php echo e($schedule->user->name); ?></td>
                    <td><?php echo e($schedule->store->name); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($schedule->start_time)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($schedule->end_time)->format('H:i')); ?></td>
                    <td>
                        <?php if($schedule->status === 'completed'): ?>
                            <span class="badge badge-success">Completed</span>
                        <?php elseif($schedule->status === 'missed'): ?>
                            <span class="badge badge-danger">Missed</span>
                        <?php elseif($schedule->status === 'cancelled'): ?>
                            <span class="badge badge-warning">Cancelled</span>
                        <?php else: ?>
                            <span class="badge badge-info">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($schedule->checkIn): ?>
                            <span class="badge badge-<?php echo e($schedule->checkIn->is_verified ? 'success' : 'danger'); ?>">
                                <?php echo e($schedule->checkIn->is_verified ? '✓' : '✗'); ?> <?php echo e($schedule->checkIn->distance_from_store); ?>m
                            </span>
                        <?php else: ?>
                            <span class="text-muted text-sm">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex-row">
                            <a href="/admin/schedules/<?php echo e($schedule->id); ?>/edit" class="btn btn-sm btn-outline">Edit</a>
                            <form method="POST" action="/admin/schedules/<?php echo e($schedule->id); ?>" onsubmit="return confirm('Delete?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-muted" style="text-align:center;padding:24px;">No schedules found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination"><?php echo e($schedules->appends(request()->query())->links('pagination::simple-default')); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/schedules/index.blade.php ENDPATH**/ ?>