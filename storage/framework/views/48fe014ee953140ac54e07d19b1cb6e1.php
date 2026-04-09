<?php $__env->startSection('title', 'Create Schedule'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Create Schedule</h1>
        <div class="breadcrumb"><a href="/admin/schedules">Schedules</a> &bull; Create New</div>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($error); ?><br> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/schedules">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label class="form-label">Area Manager *</label>
            <select name="user_id" class="form-control" required>
                <option value="">Select AM</option>
                <?php $__currentLoopData = $ams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $am): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($am->id); ?>" <?php echo e(old('user_id') == $am->id ? 'selected' : ''); ?>><?php echo e($am->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Store *</label>
            <select name="store_id" class="form-control" required>
                <option value="">Select Store</option>
                <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($store->id); ?>" <?php echo e(old('store_id') == $store->id ? 'selected' : ''); ?>><?php echo e($store->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Date *</label>
            <input type="date" name="scheduled_date" class="form-control" value="<?php echo e(old('scheduled_date')); ?>" required>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Start Time *</label>
                <input type="time" name="start_time" class="form-control" value="<?php echo e(old('start_time', '09:00')); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">End Time *</label>
                <input type="time" name="end_time" class="form-control" value="<?php echo e(old('end_time', '11:00')); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control"><?php echo e(old('notes')); ?></textarea>
        </div>
        <div class="flex-row">
            <button type="submit" class="btn btn-primary">Create Schedule</button>
            <a href="/admin/schedules" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/schedules/create.blade.php ENDPATH**/ ?>