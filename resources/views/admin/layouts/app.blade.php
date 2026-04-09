<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'F2M Field Management') - Admin Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --bg-input: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --accent: #C41230;
            --accent-hover: #e63946;
            --success: #16a34a;
            --success-bg: rgba(22,163,74,0.15);
            --warning: #d97706;
            --warning-bg: rgba(217,119,6,0.15);
            --danger: #dc2626;
            --danger-bg: rgba(220,38,38,0.15);
            --info: #2563eb;
            --info-bg: rgba(37,99,235,0.15);
            --border: #e2e8f0;
            --sidebar-width: 280px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            font-size: 16px;
        }
        a { color: var(--accent); text-decoration: none; transition: color 0.2s; }
        a:hover { color: var(--accent-hover); }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            display: flex; flex-direction: column;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-brand .logo {
            width: 48px; height: 48px;
            background: var(--accent);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 20px; color: white;
        }
        .sidebar-brand h2 { font-size: 18px; font-weight: 700; color: var(--text-primary); }
        .sidebar-brand small { color: var(--text-muted); font-size: 13px; display: block; }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .sidebar-nav .nav-label {
            font-size: 11px; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); padding: 12px 16px 6px; font-weight: 600;
        }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; margin: 4px 0;
            border-radius: 8px; color: var(--text-secondary);
            font-size: 16px; transition: all 0.2s; font-weight: 500;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(99,102,241,0.15); color: var(--accent);
        }
        .sidebar-nav a .icon { font-size: 18px; width: 24px; text-align: center; }
        .sidebar-footer {
            padding: 16px; border-top: 1px solid var(--border);
        }
        .sidebar-footer .user-info {
            display: flex; align-items: center; gap: 12px; margin-bottom: 12px;
        }
        .sidebar-footer .user-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: var(--accent); color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 16px;
        }
        .sidebar-footer .user-name { font-size: 15px; font-weight: 600; color: var(--text-primary); }
        .sidebar-footer .user-role { font-size: 13px; color: var(--text-muted); }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1; padding: 32px;
            min-height: 100vh;
        }
        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 28px;
        }
        .page-header h1 { font-size: 24px; font-weight: 700; }
        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: var(--text-muted); margin-top: 6px;
        }

        /* Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
        }
        .card-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px;
        }
        .card-header h3 { font-size: 16px; font-weight: 600; }

        /* Stats Grid */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px; margin-bottom: 28px;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex; justify-content: space-between; align-items: flex-start;
        }
        .stat-card .stat-value {
            font-size: 28px; font-weight: 700; display: block;
        }
        .stat-card .stat-label {
            font-size: 13px; color: var(--text-muted); margin-top: 4px;
        }
        .stat-card .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }
        .stat-icon.blue { background: var(--info-bg); color: var(--info); }
        .stat-icon.green { background: var(--success-bg); color: var(--success); }
        .stat-icon.yellow { background: var(--warning-bg); color: var(--warning); }
        .stat-icon.red { background: var(--danger-bg); color: var(--danger); }

        /* Tables */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        th {
            padding: 14px 16px; text-align: left;
            font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--text-muted); font-weight: 700;
            border-bottom: 2px solid var(--border);
        }
        td {
            padding: 16px; font-size: 15px;
            border-bottom: 1px solid var(--border);
            color: var(--text-secondary);
        }
        tr:hover td { background: rgba(196,18,48,0.05); }

        /* Badges */
        .badge {
            display: inline-flex; align-items: center; padding: 4px 10px;
            border-radius: 6px; font-size: 12px; font-weight: 600;
        }
        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-warning { background: var(--warning-bg); color: var(--warning); }
        .badge-danger { background: var(--danger-bg); color: var(--danger); }
        .badge-info { background: var(--info-bg); color: var(--info); }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border: none; border-radius: 8px;
            font-size: 14px; font-weight: 500; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: var(--accent-hover); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-outline {
            background: transparent; border: 1px solid var(--border);
            color: var(--text-secondary);
        }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }

        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block; margin-bottom: 6px;
            font-size: 13px; font-weight: 500; color: var(--text-secondary);
        }
        .form-control {
            width: 100%; padding: 12px 16px;
            background: var(--bg-input); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text-primary);
            font-size: 16px; transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none; border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(196,18,48,0.2);
        }
        select.form-control { appearance: auto; }
        textarea.form-control { resize: vertical; min-height: 80px; }

        /* Alert */
        .alert {
            padding: 14px 18px; border-radius: 8px;
            margin-bottom: 20px; font-size: 14px;
        }
        .alert-success { background: var(--success-bg); color: var(--success); border: 1px solid rgba(34,197,94,0.3); }
        .alert-danger { background: var(--danger-bg); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); }

        /* Pagination */
        .pagination {
            display: flex; gap: 4px; list-style: none;
            padding: 16px 0; justify-content: center;
        }
        .pagination a, .pagination span {
            padding: 8px 14px; border-radius: 6px; font-size: 13px;
            border: 1px solid var(--border); color: var(--text-secondary);
        }
        .pagination .active span { background: var(--accent); color: white; border-color: var(--accent); }

        /* Grid helpers */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .flex-row { display: flex; gap: 12px; align-items: center; }
        .ml-auto { margin-left: auto; }
        .text-muted { color: var(--text-muted); }
        .text-sm { font-size: 13px; }
        .mt-2 { margin-top: 8px; }
        .mb-4 { margin-bottom: 16px; }

        /* Map */
        .map-container { height: 300px; border-radius: 8px; overflow: hidden; margin-top: 12px; }

        /* Responsive */
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; padding: 16px; }
        }
    </style>
</head>
<body>
    @auth
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="logo">F2M</div>
            <div>
                <h2>F2M Field Mgmt</h2>
                <small>Admin Dashboard</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Main</div>
            <a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> Dashboard
            </a>

            <div class="nav-label">Management</div>
            <a href="/admin/users" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                <span class="icon">👥</span> Area Managers
            </a>
            <a href="/admin/stores" class="{{ request()->is('admin/stores*') ? 'active' : '' }}">
                <span class="icon">🏪</span> Stores
            </a>
            <a href="/admin/schedules" class="{{ request()->is('admin/schedules*') ? 'active' : '' }}">
                <span class="icon">📅</span> Schedules
            </a>

            <div class="nav-label">Monitoring</div>
            <a href="/admin/check-ins" class="{{ request()->is('admin/check-ins*') ? 'active' : '' }}">
                <span class="icon">📍</span> Check-ins
            </a>
            <a href="/admin/activity-logs" class="{{ request()->is('admin/activity-logs*') ? 'active' : '' }}">
                <span class="icon">📋</span> Activity Logs
            </a>
            <a href="/admin/sop-reports" class="{{ request()->is('admin/sop-reports*') ? 'active' : '' }}">
                <span class="icon">✅</span> SOP Reports
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div>
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <form method="POST" action="/admin/logout">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">
                    Logout
                </button>
            </form>
        </div>
    </aside>
    @endauth

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @yield('scripts')
</body>
</html>
