<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - F2M Field Management</title>
    <style>
        :root {
            --bg-primary: #f8fafc;
            --bg-card: #ffffff;
            --bg-input: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --accent: #C41230;
            --accent-hover: #e63946;
            --border: #e2e8f0;
            --danger: #dc2626;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(196,18,48,0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(230,57,70,0.06) 0%, transparent 50%);
            font-size: 16px;
        }
        .login-container {
            width: 100%; max-width: 420px; padding: 20px;
        }
        .login-brand {
            text-align: center; margin-bottom: 36px;
        }
        .login-brand .logo {
            width: 72px; height: 72px; margin: 0 auto 20px;
            background: var(--accent);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 28px; color: white;
        }
        .login-brand h1 { font-size: 28px; font-weight: 700; }
        .login-brand p { color: var(--text-secondary); font-size: 14px; margin-top: 6px; }
        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
        }
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block; margin-bottom: 8px;
            font-size: 13px; font-weight: 500; color: var(--text-secondary);
        }
        .form-control {
            width: 100%; padding: 14px 18px;
            background: var(--bg-input); border: 1px solid var(--border);
            border-radius: 10px; color: var(--text-primary);
            font-size: 16px; transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none; border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(196,18,48,0.25);
        }
        .btn-login {
            width: 100%; padding: 14px;
            background: var(--accent);
            color: white; border: none; border-radius: 10px;
            font-size: 18px; font-weight: 600; cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-login:hover { opacity: 0.9; }
        .error-msg {
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3);
            color: var(--danger); padding: 12px; border-radius: 8px;
            font-size: 13px; margin-bottom: 20px;
        }
        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 20px; font-size: 13px; color: var(--text-secondary);
        }
        .remember-row input { accent-color: var(--accent); }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-brand">
            <div class="logo">F2M</div>
            <h1>F2M Field Mgmt</h1>
            <p>Admin Dashboard Login</p>
        </div>
        <div class="login-card">
            <?php if($errors->any()): ?>
                <div class="error-msg">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($error); ?><br>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="/admin/login">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control"
                        value="<?php echo e(old('email')); ?>" placeholder="admin@amtracker.com" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control"
                        placeholder="••••••••" required>
                </div>
                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\kelvin.yohandi\.gemini\antigravity\scratch\am-tracker-api\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>