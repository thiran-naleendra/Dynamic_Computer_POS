<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dynamic Computer Systems</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --app-bg: #edf3f8;
            --app-bg-soft: #f7fafc;
            --surface: rgba(255, 255, 255, 0.88);
            --surface-strong: #ffffff;
            --surface-soft: #f4f8fc;
            --text-main: #18324d;
            --text-muted: #6c8098;
            --border-soft: rgba(24, 50, 77, 0.1);
            --shadow-soft: 0 14px 36px rgba(15, 23, 42, 0.08);
            --brand: #2457a7;
            --brand-deep: #1b407a;
            --brand-soft: rgba(36, 87, 167, 0.08);
            --success-soft: rgba(25, 135, 84, 0.1);
        }

        html[data-theme="dark"] {
            --app-bg: #0e1726;
            --app-bg-soft: #121f31;
            --surface: rgba(19, 31, 49, 0.9);
            --surface-strong: #162338;
            --surface-soft: #1b2b43;
            --text-main: #e6eef8;
            --text-muted: #9fb0c4;
            --border-soft: rgba(255, 255, 255, 0.08);
            --shadow-soft: 0 18px 42px rgba(0, 0, 0, 0.28);
            --brand: #6ea7ff;
            --brand-deep: #7bb0ff;
            --brand-soft: rgba(110, 167, 255, 0.1);
            --success-soft: rgba(45, 190, 115, 0.12);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            color: var(--text-main);
            background:
                radial-gradient(circle at top left, rgba(36, 87, 167, 0.08), transparent 22%),
                linear-gradient(180deg, var(--app-bg-soft), var(--app-bg));
            font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
        }

        ::selection {
            background: rgba(36, 87, 167, 0.22);
            color: var(--text-main);
        }

        html[data-theme="dark"] ::selection {
            background: rgba(110, 167, 255, 0.24);
            color: #fff;
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(36, 87, 167, 0.35) transparent;
        }

        html[data-theme="dark"] * {
            scrollbar-color: rgba(110, 167, 255, 0.32) transparent;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        hr {
            border-color: var(--border-soft);
            opacity: 1;
        }

        .app-shell {
            position: relative;
        }

        .app-shell::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at right bottom, rgba(40, 167, 69, 0.06), transparent 18%);
            z-index: -1;
        }

        .app-header {
            position: sticky;
            top: 0;
            z-index: 1035;
            padding: 0 0 .75rem;
            backdrop-filter: blur(10px);
        }

        .app-nav {
            border: 0;
            border-radius: 0 0 20px 20px;
            background: var(--surface);
            box-shadow: var(--shadow-soft);
        }

        .app-nav .container-fluid {
            padding: .9rem 1.4rem;
        }

        .brand-block {
            display: flex;
            align-items: center;
            gap: .85rem;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--brand-deep), var(--brand));
            color: #fff;
            font-size: 1.15rem;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
        }

        .brand-copy {
            line-height: 1.05;
        }

        .brand-title {
            display: block;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .brand-subtitle {
            display: block;
            margin-top: .18rem;
            font-size: .74rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .app-nav .navbar-toggler {
            border-color: var(--border-soft);
            padding: .45rem .7rem;
        }

        .app-nav .navbar-toggler:focus {
            box-shadow: none;
        }

        .nav-cluster {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .nav-link-chip {
            display: inline-flex;
            align-items: center;
            gap: 0;
            padding: .58rem .82rem;
            border-radius: 12px;
            color: var(--text-muted) !important;
            font-weight: 700;
            font-size: .94rem;
            transition: all .18s ease;
        }

        .nav-link-chip:hover {
            background: var(--brand-soft);
            color: var(--brand) !important;
        }

        .nav-link-chip.active {
            background: linear-gradient(135deg, var(--brand-deep), var(--brand));
            color: #fff !important;
            box-shadow: 0 10px 22px rgba(36, 87, 167, 0.18);
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: .65rem;
            flex-wrap: wrap;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .5rem .75rem;
            border-radius: 14px;
            background: var(--brand-soft);
            color: var(--text-main);
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--brand-deep), var(--brand));
            color: #fff;
            font-weight: 800;
        }

        .user-meta {
            line-height: 1.05;
        }

        .user-meta strong {
            display: block;
            font-size: .9rem;
        }

        .user-meta small {
            display: block;
            color: var(--text-muted);
            font-size: .72rem;
        }

        .theme-btn,
        .logout-btn {
            border-radius: 12px;
            font-weight: 700;
            padding: .58rem .9rem;
        }

        .theme-btn {
            border: 1px solid var(--border-soft);
            background: var(--surface-strong);
            color: var(--text-main);
        }

        .theme-btn:hover {
            background: var(--brand-soft);
            color: var(--brand);
        }

        .logout-btn {
            border: 0;
            background: linear-gradient(135deg, var(--brand-deep), var(--brand));
            color: #fff;
        }

        .logout-btn:hover {
            color: #fff;
            opacity: .94;
        }

        .top-strip {
            margin-bottom: 1.1rem;
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            background: var(--surface);
            box-shadow: var(--shadow-soft);
        }

        .top-strip-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: .9rem 1rem;
        }

        .top-strip-copy {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .top-strip-icon {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #22a55a;
            box-shadow: 0 0 0 0 rgba(34, 165, 90, 0.35);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 165, 90, 0.35); }
            70% { box-shadow: 0 0 0 11px rgba(34, 165, 90, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 165, 90, 0); }
        }

        .top-strip-copy strong {
            display: block;
            font-size: 1rem;
        }

        .top-strip-copy small {
            color: var(--text-muted);
        }

        .top-strip-meta {
            display: flex;
            gap: .55rem;
            flex-wrap: wrap;
        }

        .meta-chip {
            display: inline-flex;
            align-items: center;
            padding: .45rem .75rem;
            border-radius: 999px;
            background: var(--brand-soft);
            color: var(--text-main);
            font-size: .84rem;
            font-weight: 700;
        }

        .page-main {
            padding-bottom: 1.8rem;
        }

        .page-body {
            min-height: calc(100vh - 210px);
        }

        .card,
        .alert,
        .modal-content,
        .dropdown-menu {
            border-color: var(--border-soft);
        }

        .btn {
            border-radius: 12px;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .form-control,
        .form-select {
            border-color: var(--border-soft);
            background: var(--surface-strong);
            color: var(--text-main);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: color-mix(in srgb, var(--brand) 55%, transparent);
            box-shadow: 0 0 0 .22rem color-mix(in srgb, var(--brand) 18%, transparent);
        }

        .form-control::placeholder {
            color: color-mix(in srgb, var(--text-muted) 88%, transparent);
        }

        .input-group-text {
            border-color: var(--border-soft);
            background: var(--surface-soft);
            color: var(--text-muted);
        }

        .table {
            color: var(--text-main);
        }

        .table-light,
        thead.table-light {
            background: rgba(36, 87, 167, 0.06) !important;
            color: var(--text-main) !important;
        }

        .app-footer {
            padding-bottom: 1rem;
        }

        .footer-card {
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            background: var(--surface);
            box-shadow: var(--shadow-soft);
            padding: 1rem 1.1rem;
        }

        .footer-copy {
            color: var(--text-muted);
        }

        .footer-tags {
            display: flex;
            gap: .45rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .footer-tag {
            padding: .4rem .65rem;
            border-radius: 999px;
            background: var(--brand-soft);
            font-size: .8rem;
            font-weight: 700;
        }

        .bg-light,
        .bg-body-tertiary {
            background: var(--surface-soft) !important;
            color: var(--text-main) !important;
        }

        .text-dark {
            color: var(--text-main) !important;
        }

        .badge.text-bg-light {
            background: var(--surface-soft) !important;
            color: var(--text-main) !important;
            border: 1px solid var(--border-soft);
        }

        .alert {
            background: var(--surface);
            color: var(--text-main);
        }

        .alert-success {
            background: color-mix(in srgb, #198754 11%, var(--surface-strong)) !important;
            border-color: color-mix(in srgb, #198754 18%, transparent) !important;
        }

        .alert-danger {
            background: color-mix(in srgb, #dc3545 11%, var(--surface-strong)) !important;
            border-color: color-mix(in srgb, #dc3545 18%, transparent) !important;
        }

        .alert-warning {
            background: color-mix(in srgb, #ffc107 12%, var(--surface-strong)) !important;
            border-color: color-mix(in srgb, #ffc107 20%, transparent) !important;
        }

        .alert-primary {
            background: color-mix(in srgb, #0d6efd 10%, var(--surface-strong)) !important;
            border-color: color-mix(in srgb, #0d6efd 18%, transparent) !important;
        }

        html[data-theme="dark"] .card,
        html[data-theme="dark"] .alert,
        html[data-theme="dark"] .modal-content,
        html[data-theme="dark"] .dropdown-menu,
        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .form-select,
        html[data-theme="dark"] .theme-btn {
            background: var(--surface-strong) !important;
            color: var(--text-main) !important;
            border-color: var(--border-soft) !important;
        }

        html[data-theme="dark"] .table,
        html[data-theme="dark"] table.dataTable,
        html[data-theme="dark"] table.dataTable tbody td {
            color: var(--text-main) !important;
            background: var(--surface-strong) !important;
            border-color: var(--border-soft) !important;
        }

        html[data-theme="dark"] .table > :not(caption) > * > * {
            background-color: transparent;
            box-shadow: none;
        }

        html[data-theme="dark"] .table-light,
        html[data-theme="dark"] thead.table-light,
        html[data-theme="dark"] table.dataTable thead th,
        html[data-theme="dark"] table.dataTable thead td {
            background: #1a2a42 !important;
            color: var(--text-main) !important;
            border-color: var(--border-soft) !important;
        }

        html[data-theme="dark"] .table-hover > tbody > tr:hover > * {
            background-color: rgba(110, 167, 255, 0.06) !important;
            color: var(--text-main) !important;
        }

        html[data-theme="dark"] .dataTables_wrapper,
        html[data-theme="dark"] .dataTables_wrapper label,
        html[data-theme="dark"] .dataTables_wrapper .dataTables_info,
        html[data-theme="dark"] .dataTables_wrapper .dataTables_length,
        html[data-theme="dark"] .dataTables_wrapper .dataTables_filter,
        html[data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: var(--text-main) !important;
        }

        html[data-theme="dark"] .dataTables_wrapper .dataTables_filter input,
        html[data-theme="dark"] .dataTables_wrapper .dataTables_length select {
            background: var(--surface-strong) !important;
            color: var(--text-main) !important;
            border-color: var(--border-soft) !important;
        }

        html[data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button:hover,
        html[data-theme="dark"] .page-link:hover {
            background: rgba(110, 167, 255, 0.12) !important;
            color: var(--text-main) !important;
            border-color: var(--border-soft) !important;
        }

        html[data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        html[data-theme="dark"] .page-item.active .page-link {
            background: linear-gradient(135deg, var(--brand-deep), var(--brand)) !important;
            border-color: transparent !important;
            color: #fff !important;
        }

        html[data-theme="dark"] .page-link,
        html[data-theme="dark"] .page-item.disabled .page-link {
            background: var(--surface-strong) !important;
            color: var(--text-main) !important;
            border-color: var(--border-soft) !important;
        }

        html[data-theme="dark"] .btn-outline-secondary,
        html[data-theme="dark"] .btn-outline-primary,
        html[data-theme="dark"] .btn-outline-warning,
        html[data-theme="dark"] .btn-outline-danger,
        html[data-theme="dark"] .btn-outline-dark {
            color: var(--text-main);
            border-color: var(--border-soft);
            background: transparent;
        }

        html[data-theme="dark"] .btn-outline-secondary:hover,
        html[data-theme="dark"] .btn-outline-primary:hover,
        html[data-theme="dark"] .btn-outline-warning:hover,
        html[data-theme="dark"] .btn-outline-danger:hover,
        html[data-theme="dark"] .btn-outline-dark:hover {
            background: rgba(110, 167, 255, 0.1);
            color: #fff;
            border-color: rgba(110, 167, 255, 0.2);
        }

        html[data-theme="dark"] .btn-light {
            background: var(--surface-soft);
            color: var(--text-main);
            border-color: var(--border-soft);
        }

        html[data-theme="dark"] .btn-light:hover {
            background: rgba(110, 167, 255, 0.12);
            color: #fff;
        }

        html[data-theme="dark"] .bg-light.rounded,
        html[data-theme="dark"] .p-3.bg-light,
        html[data-theme="dark"] .rounded.bg-light {
            background: var(--surface-soft) !important;
            border: 1px solid var(--border-soft);
        }

        html[data-theme="dark"] .badge.bg-secondary {
            background: #33465f !important;
        }

        html[data-theme="dark"] .badge.bg-primary {
            background: #2457a7 !important;
        }

        html[data-theme="dark"] .badge.bg-success {
            background: #1c7a4f !important;
        }

        html[data-theme="dark"] .badge.bg-warning {
            background: #b18a12 !important;
            color: #111 !important;
        }

        html[data-theme="dark"] .badge.bg-danger {
            background: #a13344 !important;
        }

        html[data-theme="dark"] .badge.bg-info {
            background: #0e7490 !important;
            color: #eaf9ff !important;
        }

        html[data-theme="dark"] .select2-container--default .select2-selection--single,
        html[data-theme="dark"] .select2-container--default .select2-selection--multiple {
            background: var(--surface-strong) !important;
            border-color: var(--border-soft) !important;
            color: var(--text-main) !important;
        }

        html[data-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered,
        html[data-theme="dark"] .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            color: var(--text-main) !important;
        }

        html[data-theme="dark"] .select2-dropdown {
            background: var(--surface-strong) !important;
            border-color: var(--border-soft) !important;
        }

        html[data-theme="dark"] .select2-search__field {
            background: var(--surface-soft) !important;
            color: var(--text-main) !important;
            border-color: var(--border-soft) !important;
        }

        html[data-theme="dark"] .select2-results__option {
            color: var(--text-main) !important;
        }

        html[data-theme="dark"] .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: rgba(110, 167, 255, 0.14) !important;
            color: #fff !important;
        }

        @media (max-width: 1199.98px) {
            .toolbar {
                margin-top: .85rem;
            }
        }

        @media (max-width: 991.98px) {
            .top-strip-inner,
            .footer-card {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .footer-tags {
                justify-content: flex-start;
            }
        }

        @media (max-width: 575.98px) {
            .brand-subtitle,
            .user-meta small,
            .top-strip-copy small {
                display: none;
            }

            .nav-link-chip {
                width: 100%;
                justify-content: flex-start;
            }

            .toolbar,
            .top-strip-meta {
                width: 100%;
            }

            .theme-btn,
            .logout-btn {
                flex: 1 1 auto;
            }

            .app-nav .container-fluid {
                padding: .85rem 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="app-shell d-flex flex-column min-vh-100">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

        <div class="app-header">
            <nav class="navbar navbar-expand-xl app-nav">
                <div class="container-fluid">
                    <a class="navbar-brand me-4" href="{{ route('dashboard') }}">
                        <span class="brand-block">
                            <span class="brand-mark">⚡</span>
                            <span class="brand-copy">
                                <span class="brand-title">Dynamic Computer Systems</span>
                                <span class="brand-subtitle">Retail Operations Hub</span>
                            </span>
                        </span>
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                        <div class="collapse navbar-collapse" id="mainNavbar">
                            @auth
                                <div class="nav-cluster me-auto mt-3 mt-xl-0">
                                    <a class="nav-link-chip {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                        <span>Dashboard</span>
                                    </a>
                                    <a class="nav-link-chip {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
                                        <span>Invoices</span>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <a class="nav-link-chip {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                            <span>Products</span>
                                        </a>
                                        <a class="nav-link-chip {{ request()->routeIs('stock.*') ? 'active' : '' }}" href="{{ route('stock.index') }}">
                                            <span>Stock</span>
                                        </a>
                                        <a class="nav-link-chip {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                                            <span>Customers</span>
                                        </a>
                                        <a class="nav-link-chip {{ request()->routeIs('returns.*') ? 'active' : '' }}" href="{{ route('returns.index') }}">
                                            <span>Returns</span>
                                        </a>
                                        <a class="nav-link-chip {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                            <span>Reports</span>
                                        </a>
                                        <a class="nav-link-chip {{ request()->routeIs('service-notes.*') ? 'active' : '' }}" href="{{ route('service-notes.index') }}">
                                            <span>Service</span>
                                        </a>
                                        <a class="nav-link-chip {{ request()->routeIs('users.*') || request()->routeIs('register') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                            <span>Users</span>
                                        </a>
                                    @endif
                                </div>
                            @endauth

                        <div class="toolbar ms-xl-3">
                            @auth
                                <div class="user-pill">
                                    <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                                        <span class="user-meta">
                                            <strong>{{ Auth::user()->name }}</strong>
                                            <small>{{ ucfirst(Auth::user()->role) }} • {{ now()->timezone('Asia/Colombo')->format('d M Y • h:i A') }}</small>
                                        </span>
                                    </div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="btn logout-btn" type="submit">Logout</button>
                                </form>
                            @endauth

                            <button id="themeToggle" class="btn theme-btn" type="button" aria-label="Toggle theme">
                                <span class="theme-icon">🌙</span>
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <main class="page-main flex-grow-1">
            <div class="container page-body">
                @auth
                    <div class="top-strip">
                        <div class="top-strip-inner">
                            <div class="top-strip-copy">
                                <span class="top-strip-icon"></span>
                                <div>
                                    <strong>System ready</strong>
                                    <small>Manage sales, stock, returns, and service work in one place.</small>
                                </div>
                            </div>

                            <div class="top-strip-meta">
                                <span class="meta-chip">Asia/Colombo</span>
                                <span class="meta-chip">{{ now()->timezone('Asia/Colombo')->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endauth

                @yield('content')
            </div>
        </main>

        <footer class="app-footer mt-auto">
            <div class="container">
                <div class="footer-card d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div>
                        <div class="fw-bold">Dynamic Computer Systems</div>
                        <div class="footer-copy small">System By Thiran Naleendra V 2.0.1</div>
                    </div>

                    <div class="footer-tags">
                        <span class="footer-tag">Stock</span>
                        <span class="footer-tag">Invoices</span>
                        <span class="footer-tag">Reports</span>
                        <span class="footer-tag">Service</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const html = document.documentElement;
            const toggleBtn = document.getElementById('themeToggle');
            const iconEl = toggleBtn ? toggleBtn.querySelector('.theme-icon') : null;
            const savedTheme = localStorage.getItem('theme');
            const initialTheme = savedTheme || 'light';

            function applyTheme(theme) {
                html.setAttribute('data-theme', theme);
                if (iconEl) {
                    iconEl.textContent = theme === 'dark' ? '☀️' : '🌙';
                }
            }

            applyTheme(initialTheme);

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const nextTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('theme', nextTheme);
                    applyTheme(nextTheme);
                });
            }
        })();
    </script>
</body>

</html>
