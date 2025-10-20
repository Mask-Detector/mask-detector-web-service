<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mask Detection Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 80px;
            background: #2c3e50;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-logo {
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 3rem;
            writing-mode: vertical-rl;
            text-orientation: mixed;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }

        .sidebar-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            padding: 0.75rem 0;
            transition: all 0.3s;
            font-size: 0.75rem;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            color: #3498db;
            background: rgba(52, 152, 219, 0.1);
        }

        .sidebar-link svg {
            width: 24px;
            height: 24px;
        }

        /* Top Bar */
        .topbar {
            position: fixed;
            left: 80px;
            top: 0;
            right: 0;
            height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 999;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-title {
            color: #2c3e50;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 0;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-help {
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-help:hover {
            background: #229954;
            transform: translateY(-1px);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-name {
            color: #2c3e50;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db, #2980b9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        /* Main Content */
        .main-content {
            margin-left: 80px;
            margin-top: 70px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .sidebar-logo {
                font-size: 0.9rem;
            }

            .sidebar-link {
                font-size: 0.65rem;
            }

            .sidebar-link svg {
                width: 20px;
                height: 20px;
            }

            .topbar {
                left: 60px;
                padding: 0 1rem;
            }

            .topbar-title {
                font-size: 0.9rem;
            }

            .user-name {
                display: none;
            }

            .main-content {
                margin-left: 60px;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">MASK</div>
        <nav class="sidebar-nav">
            <a href="{{ url('/') }}" class="sidebar-link active">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="#" class="sidebar-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
                <span>Database</span>
            </a>
            <a href="#" class="sidebar-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
                </svg>
                <span>Search</span>
            </a>
            <a href="#" class="sidebar-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Reports</span>
            </a>
            <a href="#" class="sidebar-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1v6m0 6v6m5.196-15.196l-4.242 4.242m0 5.908l-4.242 4.242m15.196-5.196l-6 0m-6 0l-6 0m15.196 5.196l-4.242-4.242m0-5.908l-4.242-4.242"/>
                </svg>
                <span>Settings</span>
            </a>
        </nav>
    </div>

    <!-- Top Bar -->
    <div class="topbar">
        <div class="topbar-left">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="2">
                <path d="M9 2v2m6-2v2M4 6h16M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2M4 6v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6"/>
            </svg>
            <h5 class="topbar-title">Mask Detection System</h5>
        </div>
        <div class="topbar-right">
            <button class="btn-help">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Bantuan
            </button>
            <div class="user-info">
                <span class="user-name">Halo, Muhammad iqbal</span>
                <div class="user-avatar">JD</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
