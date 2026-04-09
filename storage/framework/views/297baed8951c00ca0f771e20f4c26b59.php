<?php $__env->startSection('title', 'Activity Logs'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Activity Logs</h1>
        <div class="breadcrumb">Monitoring &bull; Activity Logs</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="/admin/activity-logs" class="flex-row">
            <select name="action" class="form-control" style="max-width:200px;">
                <option value="">All Actions</option>
                <option value="login" <?php echo e(request('action') === 'login' ? 'selected' : ''); ?>>Login</option>
                <option value="logout" <?php echo e(request('action') === 'logout' ? 'selected' : ''); ?>>Logout</option>
                <option value="check_in" <?php echo e(request('action') === 'check_in' ? 'selected' : ''); ?>>Check-in</option>
                <option value="schedule_create" <?php echo e(request('action') === 'schedule_create' ? 'selected' : ''); ?>>Schedule Create</option>
                <option value="schedule_update" <?php echo e(request('action') === 'schedule_update' ? 'selected' : ''); ?>>Schedule Update</option>
                <option value="profile_update" <?php echo e(request('action') === 'profile_update' ? 'selected' : ''); ?>>Profile Update</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline">Filter</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>User</th><th>Action</th><th>Description</th><th>IP</th><th>Time</th></tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($log->user->name ?? 'Unknown'); ?></td>
                    <td><span class="badge badge-info"><?php echo e($log->action); ?></span></td>
                    <td class="text-sm"><?php echo e($log->description); ?></td>
                    <td class="text-muted text-sm"><?php echo e($log->ip_address); ?></td>
                    <td class="text-muted text-sm"><?php echo e($log->created_at->format('d M Y H:i')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-muted" style="text-align:center;padding:24px;">No activity logs</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination"><?php echo e($logs->appends(request()->query())->links('pagination::simple-default')); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/activity-logs/index.blade.php ENDPATH**/ ?>