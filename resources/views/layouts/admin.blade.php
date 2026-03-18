<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Optik Perkasa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #1e2a5e;
            --sidebar-hover: rgba(255,255,255,.1);
            --sidebar-active: rgba(255,255,255,.18);
            --topbar-height: 60px;
        }
        body { background: #f4f6fb; font-size: .9rem; }

        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 1040; overflow-y: auto;
        }
        .sidebar-brand {
            padding: 18px 20px;
            color: #fff; font-size: 1.1rem; font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,.1);
            text-decoration: none; display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand:hover { color: #fff; }
        .nav-section {
            padding: 14px 20px 4px;
            font-size: .7rem; letter-spacing: .08em; text-transform: uppercase;
            color: rgba(255,255,255,.4);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 8px 20px; display: flex; align-items: center; gap: 10px;
            border-radius: 8px; margin: 2px 10px; font-size: .85rem;
            transition: background .15s, color .15s;
        }
        .sidebar .nav-link:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar .nav-link.active { background: var(--sidebar-active); color: #fff; font-weight: 500; }
        .sidebar .nav-link .bi { font-size: 1rem; }
        .sidebar-footer {
            margin-top: auto; padding: 16px;
            border-top: 1px solid rgba(255,255,255,.1);
        }

        /* Main content */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex; align-items: center;
            padding: 0 24px; gap: 12px;
            position: sticky; top: 0; z-index: 1030;
        }
        .topbar-title { font-weight: 600; font-size: 1rem; flex: 1; }
        .content { padding: 24px; flex: 1; }

        /* Cards */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.07); }
        .card-header { background: transparent; border-bottom: 1px solid #f0f0f0; font-weight: 600; }

        /* Stat cards */
        .stat-card { border-left: 4px solid; }
        .stat-card .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }
        .stat-val { font-size: 1.6rem; font-weight: 700; line-height: 1.1; }
        .stat-label { font-size: .78rem; color: #6c757d; margin-bottom: 2px; }

        /* Table */
        .table th { font-size: .78rem; font-weight: 600; text-transform: uppercase;
                    letter-spacing: .04em; color: #6c757d; border-top: none; }
        .table td { vertical-align: middle; }

        /* Badge status */
        .badge-lunas   { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-batal   { background: #fee2e2; color: #991b1b; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .3s; }
            .sidebar.show { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<nav class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <i class="bi bi-eyeglasses fs-4"></i>
        <span>Optik Perkasa</span>
    </a>

    <div class="nav-section">Menu</div>

    <a href="{{ route('dashboard') }}"
       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-section">Pasien</div>

    <a href="{{ route('patients.index') }}"
       class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Data Pasien
    </a>

    @canany(['medical_record.view'])
    <a href="{{ route('medical-records.index') }}"
       class="nav-link {{ request()->routeIs('medical-records.*') ? 'active' : '' }}">
        <i class="bi bi-clipboard2-pulse"></i> Rekam Medis
    </a>
    @endcanany

    <div class="nav-section">Toko</div>

    <a href="{{ route('transactions.create') }}"
       class="nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
        <i class="bi bi-cart-plus"></i> POS / Kasir
    </a>

    <a href="{{ route('transactions.index') }}"
       class="nav-link {{ request()->routeIs('transactions.index','transactions.show') ? 'active' : '' }}">
        <i class="bi bi-receipt-cutoff"></i> Riwayat Transaksi
    </a>

    @canany(['product.view'])
    <a href="{{ route('products.index') }}"
       class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> Produk
    </a>

    <a href="{{ route('categories.index') }}"
       class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
        <i class="bi bi-tags"></i> Kategori
    </a>
    @endcanany

    @canany(['report.view'])
    <div class="nav-section">Laporan</div>

    <a href="{{ route('reports.penjualan') }}"
       class="nav-link {{ request()->routeIs('reports.penjualan') ? 'active' : '' }}">
        <i class="bi bi-graph-up-arrow"></i> Lap. Penjualan
    </a>

    <a href="{{ route('reports.produk-terlaris') }}"
       class="nav-link {{ request()->routeIs('reports.produk-terlaris') ? 'active' : '' }}">
        <i class="bi bi-trophy"></i> Produk Terlaris
    </a>

    <a href="{{ route('reports.stok') }}"
       class="nav-link {{ request()->routeIs('reports.stok') ? 'active' : '' }}">
        <i class="bi bi-boxes"></i> Lap. Stok
    </a>
    @endcanany

    @if(auth()->user()->hasRole('super_admin'))
    <div class="nav-section">Sistem</div>

    <a href="{{ route('users.index') }}"
       class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <i class="bi bi-person-gear"></i> Manajemen User
    </a>
    @endif

    <div class="sidebar-footer">
        <div class="text-white-50 small mb-2">
            <i class="bi bi-person-circle me-1"></i>
            {{ auth()->user()->name }}
            <span class="badge bg-secondary ms-1" style="font-size:.65rem">
                {{ auth()->user()->getRoleNames()->first() }}
            </span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-light btn-sm w-100">
                <i class="bi bi-box-arrow-left me-1"></i> Logout
            </button>
        </form>
    </div>
</nav>

{{-- MAIN --}}
<div class="main-wrapper">
    {{-- TOPBAR --}}
    <div class="topbar">
        <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="text-muted small d-none d-md-block">
            {{ now()->isoFormat('dddd, D MMMM Y') }}
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="content">
        {{-- Alert messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Terdapat kesalahan input:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
