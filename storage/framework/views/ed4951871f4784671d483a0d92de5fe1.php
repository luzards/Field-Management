<?php $__env->startSection('title', 'Stores'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Stores</h1>
        <div class="breadcrumb">Management &bull; Stores</div>
    </div>
    <a href="/admin/stores/create" class="btn btn-primary">+ New Store</a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="/admin/stores" class="flex-row">
            <input type="text" name="search" class="form-control" style="max-width:300px;"
                placeholder="Search store name or address..." value="<?php echo e(request('search')); ?>">
            <button type="submit" class="btn btn-sm btn-outline">Search</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Name</th><th>Address</th><th>Contact</th><th>Schedules</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($store->name); ?></strong></td>
                    <td class="text-muted text-sm"><?php echo e(Str::limit($store->address, 40)); ?></td>
                    <td><?php echo e($store->contact_name ?? '-'); ?> <br><span class="text-muted text-sm"><?php echo e($store->contact_phone); ?></span></td>
                    <td><?php echo e($store->schedules_count); ?></td>
                    <td>
                        <span class="badge badge-<?php echo e($store->is_active ? 'success' : 'danger'); ?>">
                            <?php echo e($store->is_active ? 'Active' : 'Inactive'); ?>

                        </span>
                    </td>
                    <td>
                        <div class="flex-row">
                            <a href="/admin/stores/<?php echo e($store->id); ?>/edit" class="btn btn-sm btn-outline">Edit</a>
                            <form method="POST" action="/admin/stores/<?php echo e($store->id); ?>" onsubmit="return confirm('Delete this store?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-muted" style="text-align:center;padding:24px;">No stores found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination"><?php echo e($stores->appends(request()->query())->links('pagination::simple-default')); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/stores/index.blade.php ENDPATH**/ ?>