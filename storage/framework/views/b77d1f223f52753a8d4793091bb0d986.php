<?php $__env->startSection('title', 'Area Managers'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Area Managers</h1>
        <div class="breadcrumb">Management &bull; Area Managers</div>
    </div>
    <a href="/admin/users/create" class="btn btn-primary">+ New AM</a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="/admin/users" class="flex-row">
            <input type="text" name="search" class="form-control" style="max-width:300px;"
                placeholder="Search name, email, phone..." value="<?php echo e(request('search')); ?>">
            <button type="submit" class="btn btn-sm btn-outline">Search</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Schedules</th>
                    <th>Check-ins</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($user->name); ?></strong></td>
                    <td class="text-muted"><?php echo e($user->email); ?></td>
                    <td><?php echo e($user->phone ?? '-'); ?></td>
                    <td><?php echo e($user->schedules_count); ?></td>
                    <td><?php echo e($user->check_ins_count); ?></td>
                    <td>
                        <?php if($user->is_active): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex-row">
                            <a href="/admin/users/<?php echo e($user->id); ?>" class="btn btn-sm btn-outline">View</a>
                            <a href="/admin/users/<?php echo e($user->id); ?>/edit" class="btn btn-sm btn-outline">Edit</a>
                            <form method="POST" action="/admin/users/<?php echo e($user->id); ?>" onsubmit="return confirm('Delete this AM?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" style="text-align:center;padding:24px;" class="text-muted">No Area Managers found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination"><?php echo e($users->appends(request()->query())->links('pagination::simple-default')); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/users/index.blade.php ENDPATH**/ ?>