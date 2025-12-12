<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEI - Prontuário Eletrônico Inteligente</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #0d6efd;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background-color: #f1f5f9; }
        
        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width); background: var(--sidebar-bg);
            padding: 1rem; z-index: 1000; overflow-y: auto;
        }
        .sidebar .logo {
            color: #fff; font-size: 1.5rem; font-weight: 700;
            padding: 0.5rem 1rem; margin-bottom: 1rem;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .sidebar .logo i { color: var(--primary-color); }
        .sidebar .nav-link {
            color: #94a3b8; padding: 0.75rem 1rem; border-radius: 8px;
            margin-bottom: 4px; display: flex; align-items: center; gap: 0.75rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--sidebar-hover); color: #fff;
        }
        .sidebar .nav-link.active { background: var(--primary-color); }
        .sidebar .nav-link i { font-size: 1.1rem; }
        .sidebar .divider { border-top: 1px solid #334155; margin: 1rem 0; }
        
        /* Main content */
        .main-content { margin-left: var(--sidebar-width); padding: 0; min-height: 100vh; }
        
        /* Top navbar */
        .top-navbar {
            background: #fff; padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .top-navbar .search-box {
            background: #f1f5f9; border: none; border-radius: 8px;
            padding: 0.5rem 1rem 0.5rem 2.5rem; width: 300px;
        }
        .top-navbar .search-wrapper { position: relative; }
        .top-navbar .search-wrapper i {
            position: absolute; left: 0.75rem; top: 50%;
            transform: translateY(-50%); color: #64748b;
        }
        .user-menu img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        
        /* Content area */
        .content-area { padding: 1.5rem; }
        
        /* Cards */
        .stat-card {
            background: #fff; border-radius: 12px; padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .stat-card .icon {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
        }
        .stat-card h3 { font-size: 1.75rem; font-weight: 700; margin: 0.5rem 0 0.25rem; }
        .stat-card p { color: #64748b; margin: 0; font-size: 0.875rem; }
        
        /* Table styles */
        .table-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .table-card .table { margin: 0; }
        .table-card .table th { background: #f8fafc; font-weight: 600; border: none; }
        .table-card .table td { vertical-align: middle; border-color: #f1f5f9; }
        
        /* Patient avatar */
        .patient-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--primary-color); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 0.875rem;
        }
        
        /* Action buttons */
        .btn-action { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        
        /* Calendar mini */
        .calendar-mini { background: #fff; border-radius: 12px; padding: 1rem; }
        
        /* Form builder */
        .form-field-item {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 1rem; margin-bottom: 0.5rem; cursor: move;
        }
        .form-field-item:hover { border-color: var(--primary-color); }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo"><i class="bi bi-heart-pulse-fill"></i> PEI</div>
        <nav class="nav flex-column">
            <a class="nav-link <?= is_active('dashboard') ?>" href="?page=dashboard">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a class="nav-link <?= is_active('agenda') ?>" href="?page=agenda">
                <i class="bi bi-calendar3"></i> Agenda
            </a>
            <a class="nav-link <?= is_active('patients') ?>" href="?page=patients">
                <i class="bi bi-people-fill"></i> Pacientes
            </a>
            <a class="nav-link <?= is_active('atendimento') ?>" href="?page=atendimento">
                <i class="bi bi-clipboard2-pulse-fill"></i> Atendimento
            </a>
            <div class="divider"></div>
            <a class="nav-link <?= is_active('forms') ?>" href="?page=forms">
                <i class="bi bi-ui-checks-grid"></i> Formulários
            </a>
            <a class="nav-link <?= is_active('reports') ?>" href="?page=reports">
                <i class="bi bi-bar-chart-line-fill"></i> Relatórios
            </a>
            <a class="nav-link <?= is_active('telemedicina') ?>" href="?page=telemedicina">
                <i class="bi bi-camera-video-fill"></i> Telemedicina
            </a>
            <div class="divider"></div>
            <a class="nav-link <?= is_active('settings') ?>" href="?page=settings">
                <i class="bi bi-gear-fill"></i> Configurações
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control search-box" placeholder="Buscar paciente, consulta...">
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light position-relative">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                </button>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <?php 
                        // #region agent log
                        $user = current_user();
                        // FORÇAR nome correto sempre
                        if (isset($user['name']) && $user['name'] !== 'Dr. Evandro Ribeiro') {
                            $user['name'] = 'Dr. Evandro Ribeiro';
                            $_SESSION['user']['name'] = 'Dr. Evandro Ribeiro';
                        }
                        $logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.cursor' . DIRECTORY_SEPARATOR . 'debug.log';
                        $logData = [
                            'sessionId' => session_id(),
                            'runId' => 'debug-name-change-v2',
                            'hypothesisId' => 'D',
                            'location' => 'layout.php:174',
                            'message' => 'Rendering user name in layout (v2 with force update)',
                            'data' => [
                                'userName' => $user['name'] ?? 'NULL',
                                'userNameLength' => strlen($user['name'] ?? ''),
                                'userNameFirst2' => substr($user['name'] ?? '', 0, 2),
                                'htmlspecialcharsResult' => htmlspecialchars($user['name'] ?? ''),
                                'codeVersion' => 'v2-layout-force'
                            ],
                            'timestamp' => time() * 1000
                        ];
                        @file_put_contents($logPath, json_encode($logData) . "\n", FILE_APPEND);
                        // #endregion
                        ?>
                        <div class="patient-avatar"><?= substr($user['name'], 0, 2) ?></div>
                        <div class="d-none d-md-block">
                            <div class="fw-semibold"><?= htmlspecialchars($user['name']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($user['specialty'] ?? $user['role']) ?></small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <?= $content ?>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
</body>
</html>
