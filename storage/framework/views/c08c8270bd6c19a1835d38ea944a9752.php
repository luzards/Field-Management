<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <div class="breadcrumb">📊 Overview &bull; <?php echo e(now()->format('l, d M Y')); ?></div>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($stats['active_ams']); ?></span>
            <span class="stat-label">Active Area Managers</span>
        </div>
        <div class="stat-icon blue">👥</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($stats['total_stores']); ?></span>
            <span class="stat-label">Total Stores</span>
        </div>
        <div class="stat-icon green">🏪</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($stats['today_checkins']); ?> / <?php echo e($stats['today_schedules']); ?></span>
            <span class="stat-label">Today's Check-ins / Schedules</span>
        </div>
        <div class="stat-icon yellow">📍</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($stats['completion_rate']); ?>%</span>
            <span class="stat-label">Today's Completion Rate</span>
        </div>
        <div class="stat-icon <?php echo e($stats['completion_rate'] >= 70 ? 'green' : 'red'); ?>">📈</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($stats['today_verified']); ?></span>
            <span class="stat-label">Verified Check-ins Today</span>
        </div>
        <div class="stat-icon green">✅</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($stats['weekly_checkins']); ?></span>
            <span class="stat-label">This Week's Check-ins</span>
        </div>
        <div class="stat-icon blue">📅</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value"><?php echo e($sopStats['total_audits']); ?></span>
            <span class="stat-label">Total SOP Audits</span>
        </div>
        <div class="stat-icon green">✅</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value" style="color:<?php echo e($sopStats['avg_score'] >= 7 ? 'var(--success)' : ($sopStats['avg_score'] >= 5 ? 'var(--warning)' : 'var(--danger)')); ?>"><?php echo e($sopStats['avg_score']); ?>/10</span>
            <span class="stat-label">Avg SOP Score</span>
        </div>
        <div class="stat-icon <?php echo e($sopStats['avg_score'] >= 7 ? 'green' : ($sopStats['avg_score'] >= 5 ? 'yellow' : 'red')); ?>">⭐</div>
    </div>
</div>

<div class="grid-2">
    <!-- Today's Schedules -->
    <div class="card">
        <div class="card-header">
            <h3>📅 Today's Schedules</h3>
            <a href="/admin/schedules?date=<?php echo e(now()->format('Y-m-d')); ?>" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>AM</th>
                        <th>Store</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $todaySchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
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
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="text-muted text-sm" style="text-align:center;padding:24px;">No schedules for today</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Check-ins -->
    <div class="card">
        <div class="card-header">
            <h3>📍 Recent Check-ins</h3>
            <a href="/admin/check-ins" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>AM</th>
                        <th>Store</th>
                        <th>Verified</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentCheckIns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $checkIn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($checkIn->user->name); ?></td>
                        <td><?php echo e($checkIn->store->name); ?></td>
                        <td>
                            <?php if($checkIn->is_verified): ?>
                                <span class="badge badge-success">✓ <?php echo e($checkIn->distance_from_store); ?>m</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ <?php echo e($checkIn->distance_from_store); ?>m</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-sm text-muted"><?php echo e($checkIn->checked_in_at->diffForHumans()); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="text-muted text-sm" style="text-align:center;padding:24px;">No check-ins yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>