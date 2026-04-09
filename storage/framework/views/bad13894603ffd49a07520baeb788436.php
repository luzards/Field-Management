<?php $__env->startSection('title', 'Check-ins'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Check-ins</h1>
        <div class="breadcrumb">Monitoring &bull; Check-ins</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="/admin/check-ins" class="flex-row">
            <input type="date" name="date" class="form-control" style="max-width:180px;" value="<?php echo e(request('date')); ?>">
            <select name="verified" class="form-control" style="max-width:160px;">
                <option value="">All Status</option>
                <option value="1" <?php echo e(request('verified') === '1' ? 'selected' : ''); ?>>Verified</option>
                <option value="0" <?php echo e(request('verified') === '0' ? 'selected' : ''); ?>>Unverified</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline">Filter</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>AM</th><th>Store</th><th>Distance</th><th>Verified</th><th>Photo</th><th>Time</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $checkIns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $checkIn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($checkIn->user->name); ?></td>
                    <td><?php echo e($checkIn->store->name); ?></td>
                    <td><?php echo e($checkIn->distance_from_store); ?>m</td>
                    <td>
                        <?php if($checkIn->is_verified): ?>
                            <span class="badge badge-success">✓ Verified</span>
                        <?php else: ?>
                            <span class="badge badge-danger">✗ Failed (<?php echo e($checkIn->distance_from_store); ?>m)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo e(url('storage/' . $checkIn->photo_path)); ?>" target="_blank" class="btn btn-sm btn-outline">📷 View</a>
                    </td>
                    <td class="text-sm text-muted"><?php echo e($checkIn->checked_in_at->format('d M Y H:i')); ?></td>
                    <td>
                        <a href="/admin/check-ins/<?php echo e($checkIn->id); ?>" class="btn btn-sm btn-outline">Details</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-muted" style="text-align:center;padding:24px;">No check-ins found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination"><?php echo e($checkIns->appends(request()->query())->links('pagination::simple-default')); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/checkins/index.blade.php ENDPATH**/ ?>