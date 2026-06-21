<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نظام الفواتير')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary:    #6366f1;
            --primary-dk: #4f46e5;
            --sidebar-bg: #0f172a;
            --sidebar-w:  240px;
            --page-bg:    #f1f5f9;
            --card-bg:    #ffffff;
            --text:       #1e293b;
            --muted:      #64748b;
            --border:     #e2e8f0;
            --radius:     14px;
            --radius-sm:  8px;
            --shadow:     0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
            --shadow-md:  0 4px 24px rgba(99,102,241,.15);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Cairo', 'Segoe UI', sans-serif;
            background: var(--page-bg);
            color: var(--text);
            margin: 0;
        }

        /* ───── Sidebar ───── */
        .sidebar {
            position: fixed;
            top: 0; right: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-brand .brand-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--primary), #818cf8);
            border-radius: 12px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 20px;
            color: #fff;
            margin-bottom: 10px;
        }
        .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            margin: 0;
        }
        .sidebar-brand p { color: #94a3b8; font-size: 11px; margin: 0; }

        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-section { font-size: 10px; font-weight: 600; color: #475569; text-transform: uppercase;
                       letter-spacing: .08em; padding: 8px 10px 4px; }

        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all .18s;
            margin-bottom: 2px;
        }
        .nav-link i { font-size: 17px; }
        .nav-link:hover { background: rgba(255,255,255,.06); color: #e2e8f0; }
        .nav-link.active {
            background: linear-gradient(135deg, rgba(99,102,241,.25), rgba(99,102,241,.1));
            color: #a5b4fc;
            border: 1px solid rgba(99,102,241,.2);
        }
        .nav-link.active i { color: #818cf8; }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-footer p { color: #475569; font-size: 11px; text-align: center; margin: 0; }

        /* ───── Main layout ───── */
        .main-wrapper {
            margin-right: var(--sidebar-w);
            min-height: 100vh;
        }

        /* ───── Topbar ───── */
        .topbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar-title { font-size: 15px; font-weight: 600; color: var(--text); }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }

        /* ───── Page content ───── */
        .page-content { padding: 28px; }

        /* ───── Cards ───── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            font-weight: 600;
            font-size: 14px;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-body { padding: 20px; }
        .card-footer { background: transparent; border-top: 1px solid var(--border); padding: 14px 20px; }

        /* ───── Stat cards ───── */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }
        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }

        /* ───── Table ───── */
        .table { margin: 0; }
        .table thead th {
            background: #f8fafc;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            padding: 12px 16px;
        }
        .table tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover td { background: #f8fafc; }

        /* ───── Badges ───── */
        .badge { font-weight: 600; font-size: 11px; padding: 4px 10px; border-radius: 20px; }
        .badge-invoice  { background:#ede9fe; color:#6d28d9; }
        .badge-quote    { background:#fef3c7; color:#92400e; }
        .badge-draft    { background:#f1f5f9; color:#475569; }
        .badge-sent     { background:#dbeafe; color:#1d4ed8; }
        .badge-paid     { background:#dcfce7; color:#166534; }
        .badge-cancelled{ background:#fee2e2; color:#991b1b; }

        /* ───── Buttons ───── */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dk));
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: var(--radius-sm);
            padding: 9px 18px;
            font-size: 13.5px;
            box-shadow: 0 2px 8px rgba(99,102,241,.3);
            transition: all .18s;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(99,102,241,.4); color:#fff; }

        .btn-outline-secondary {
            border: 1px solid var(--border);
            color: var(--muted);
            background: #fff;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            padding: 7px 14px;
        }
        .btn-outline-secondary:hover { background: #f8fafc; color: var(--text); }

        .btn-icon {
            width: 34px; height: 34px; padding: 0;
            border-radius: var(--radius-sm);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 15px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--muted);
            transition: all .15s;
            cursor: pointer;
        }
        .btn-icon:hover { background: #f1f5f9; color: var(--text); }
        .btn-icon.danger:hover { background: #fee2e2; color: #dc2626; border-color: #fca5a5; }
        .btn-icon.primary:hover { background: #ede9fe; color: var(--primary); border-color: #c4b5fd; }
        .btn-icon.success:hover { background: #dcfce7; color: #16a34a; border-color: #86efac; }

        /* ───── Forms ───── */
        .form-label { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
        .form-control, .form-select {
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif;
            font-size: 13.5px;
            color: var(--text);
            padding: 9px 12px;
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
            outline: none;
        }
        .form-control-sm, .form-select-sm { padding: 6px 10px; font-size: 13px; }

        /* ───── Items table in form ───── */
        .items-table { background: transparent; }
        .items-table thead th {
            background: #f8fafc;
            font-size: 11.5px;
            border-top: 1px solid var(--border);
        }
        .items-table tbody td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; }
        .row-remove { cursor: pointer; color: #94a3b8; font-size: 18px; transition: color .15s; }
        .row-remove:hover { color: #dc2626; }

        /* ───── Totals panel ───── */
        .totals-row { display: flex; justify-content: space-between; padding: 7px 0; font-size: 14px; }
        .totals-row.grand { border-top: 2px solid var(--border); margin-top: 6px; padding-top: 12px; font-size: 17px; font-weight: 700; }
        .totals-row .val { font-weight: 600; }
        .totals-row.discount .val { color: #dc2626; }

        /* ───── Alert ───── */
        .alert { border: none; border-radius: var(--radius-sm); font-size: 13.5px; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }

        /* ───── Page header ───── */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px;
        }
        .page-header h4 { font-size: 20px; font-weight: 700; margin: 0; color: var(--text); }
        .page-header p  { font-size: 13px; color: var(--muted); margin: 2px 0 0; }

        /* ───── Empty state ───── */
        .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
        .empty-state i { font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 12px; }
        .empty-state h6 { font-weight: 600; color: #475569; margin-bottom: 6px; }

        /* ───── Scrollbar ───── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-wrapper { margin-right: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ───── Sidebar ───── --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-receipt-cutoff"></i></div>
        <h5>نظام الفواتير</h5>
        <p>الإصدار 1.0</p>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">القائمة</div>

        <a href="{{ route('invoices.index') }}"
           class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>
            الفواتير وعروض الأسعار
        </a>

        <a href="{{ route('customers.index') }}"
           class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            إدارة العملاء
        </a>

        <a href="{{ route('invoices.create') }}" class="nav-link">
            <i class="bi bi-plus-circle"></i>
            إنشاء فاتورة جديدة
        </a>
    </nav>

    <div class="sidebar-footer">
        <p>Laravel {{ app()->version() }} — PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}</p>
    </div>
</aside>

{{-- ───── Main ───── --}}
<div class="main-wrapper">

    <header class="topbar">
        <span class="topbar-title">@yield('title', 'نظام الفواتير')</span>
        <div class="topbar-actions">
            <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>إنشاء جديد
            </a>
        </div>
    </header>

    <main class="page-content">

        @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger mb-4">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <strong>يرجى مراجعة البيانات</strong>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
            <ul class="mb-0 mt-1 ps-3" style="font-size:13px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
