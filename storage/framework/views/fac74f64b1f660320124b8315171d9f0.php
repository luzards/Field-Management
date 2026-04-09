<?php $__env->startSection('title', 'Edit ' . $store->name); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Edit Store</h1>
        <div class="breadcrumb"><a href="/admin/stores">Stores</a> &bull; <?php echo e($store->name); ?></div>
    </div>
</div>

<div class="card" style="max-width: 700px;">
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($error); ?><br> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/stores/<?php echo e($store->id); ?>">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="form-group">
            <label class="form-label">Store Name *</label>
            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $store->name)); ?>" required>
        </div>
        <div class="form-group">
            <label class="form-label">Address *</label>
            <textarea name="address" class="form-control" required><?php echo e(old('address', $store->address)); ?></textarea>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Latitude *</label>
                <input type="number" name="latitude" id="lat" class="form-control" step="0.00000001"
                    value="<?php echo e(old('latitude', $store->latitude)); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Longitude *</label>
                <input type="number" name="longitude" id="lng" class="form-control" step="0.00000001"
                    value="<?php echo e(old('longitude', $store->longitude)); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">📍 Click on map to change location</label>
            <div id="map" class="map-container"></div>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Contact Name</label>
                <input type="text" name="contact_name" class="form-control" value="<?php echo e(old('contact_name', $store->contact_name)); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-control" value="<?php echo e(old('contact_phone', $store->contact_phone)); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-control">
                <option value="1" <?php echo e($store->is_active ? 'selected' : ''); ?>>Active</option>
                <option value="0" <?php echo e(!$store->is_active ? 'selected' : ''); ?>>Inactive</option>
            </select>
        </div>
        <div class="flex-row">
            <button type="submit" class="btn btn-primary">Update Store</button>
            <a href="/admin/stores" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    var lat = <?php echo e($store->latitude); ?>;
    var lng = <?php echo e($store->longitude); ?>;
    var map = L.map('map').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([lat, lng]).addTo(map);

    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById('lat').value = e.latlng.lat.toFixed(8);
        document.getElementById('lng').value = e.latlng.lng.toFixed(8);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/stores/edit.blade.php ENDPATH**/ ?>