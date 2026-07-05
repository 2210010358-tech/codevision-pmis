<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CodeVision PMIS')</title>
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
            --sidebar-bg: #0f172a;
            --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
            width: 100vw;
            min-height: 100vh;
        }

        /* Sidebar Style */
        #sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: #fff;
            transition: all 0.3s;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        #sidebar .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-weight: 700;
            font-size: 1.25rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }

        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
            border-left-color: var(--primary-color);
        }

        #sidebar .nav-link i {
            font-size: 1.1rem;
        }

        /* Content Area */
        #content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .navbar {
            background-color: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            z-index: 999;
        }

        .main-content {
            padding: 2rem;
            flex-grow: 1;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            background-color: #fff;
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Status & Priority Badges */
        .badge-priority-critical { background-color: #fecaca; color: #991b1b; }
        .badge-priority-high { background-color: #fed7aa; color: #9a3412; }
        .badge-priority-medium { background-color: #e0f2fe; color: #0369a1; }
        .badge-priority-low { background-color: #f1f5f9; color: #475569; }

        .badge-status-planning { background-color: #f1f5f9; color: #475569; }
        .badge-status-active { background-color: #dbeafe; color: #1d4ed8; }
        .badge-status-completed { background-color: #dcfce7; color: #15803d; }
        .badge-status-delayed { background-color: #fee2e2; color: #b91c1c; }
        .badge-status-onhold { background-color: #fef9c3; color: #a16207; }

        /* Notification Dropdown */
        .notification-dropdown {
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
            border: none;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            border-radius: 8px;
        }

        .notification-item {
            padding: 0.8rem 1.2rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
            color: var(--text-dark);
            transition: background-color 0.2s;
            display: block;
            text-decoration: none;
        }

        .notification-item:hover {
            background-color: #f8fafc;
        }

        .notification-item.unread {
            background-color: #f0fdf4;
            font-weight: 500;
        }

        /* Responsive Sidebar */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 28px; width: 28px; object-fit: contain; border-radius: 4px;">
                <span>CodeVision PMIS</span>
            </div>
            <div class="list-group list-group-flush flex-grow-1">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('projects.index') }}" class="nav-link {{ request()->is('projects*') ? 'active' : '' }}">
                    <i class="bi bi-kanban"></i> Projects
                </a>
                <a href="{{ route('bugs.index') }}" class="nav-link {{ request()->is('bugs*') ? 'active' : '' }}">
                    <i class="bi bi-bug"></i> Bugs
                </a>
                
                @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Leader'))
                    <a href="{{ route('workload.index') }}" class="nav-link {{ request()->is('workload*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Developer Workload
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Reports
                    </a>
                @endif
            </div>
            
            <div class="p-3 border-top border-secondary-subtle">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-person-circle fs-4 text-secondary"></i>
                    <div class="lh-sm">
                        <div class="fw-bold text-white small">{{ auth()->user()->name }}</div>
                        <div class="text-secondary small" style="font-size: 0.75rem;">{{ auth()->user()->roles->first()->name ?? 'No Role' }}</div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Area -->
        <div id="content-wrapper">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light sticky-top">
                <div class="container-fluid p-0">
                    <button class="btn btn-outline-secondary btn-sm border-0 me-3" id="sidebarCollapse">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    
                    <div class="d-flex align-items-center ms-auto gap-3">
                        <!-- Notification Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-light position-relative p-2 border-0 btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell fs-5"></i>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 fw-bold">Notifications</h6>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.readAll') }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-link p-0 text-decoration-none small text-primary" style="font-size: 0.75rem;">Mark all read</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="list-group list-group-flush">
                                    @forelse(auth()->user()->notifications->take(5) as $notification)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" id="notif-form-{{ $notification->id }}" class="m-0">
                                            @csrf
                                            <a href="#" onclick="document.getElementById('notif-form-{{ $notification->id }}').submit(); return false;" class="notification-item list-group-item list-group-item-action {{ $notification->read_at ? '' : 'unread' }}">
                                                <div>{{ $notification->data['message'] }}</div>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </a>
                                        </form>
                                    @empty
                                        <div class="p-3 text-center text-muted small">No new notifications</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content Slot -->
            <main class="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarCollapse').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
    @yield('scripts')
</body>
</html>
