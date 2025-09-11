<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Maintio' ?> - Industrielles Instandhaltungs-System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #1e293b;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #ffffff;
            background-color: #334155;
            border-radius: 0.375rem;
        }
        .main-content {
            background-color: #f8fafc;
            min-height: 100vh;
        }
        .card {
            border: none;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }
        .stat-card.danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="mb-4">
                        <h4 class="text-white text-center">
                            <i class="bi bi-gear-fill me-2"></i>Maintio
                        </h4>
                        <p class="text-muted text-center small">Instandhaltungs-System</p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item mb-1">
                            <a class="nav-link <?= (current_url() === base_url() || current_url() === base_url('dashboard')) ? 'active' : '' ?>" href="<?= base_url('/') ?>">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link <?= strpos(current_url(), 'work-orders') !== false ? 'active' : '' ?>" href="<?= base_url('work-orders') ?>">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Arbeitsaufträge
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link <?= strpos(current_url(), 'assets') !== false ? 'active' : '' ?>" href="<?= base_url('assets') ?>">
                                <i class="bi bi-cpu me-2"></i>
                                Anlagen
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link" href="#">
                                <i class="bi bi-bar-chart me-2"></i>
                                Berichte
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link" href="#">
                                <i class="bi bi-people me-2"></i>
                                Benutzer
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link" href="#">
                                <i class="bi bi-gear me-2"></i>
                                Einstellungen
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top Navigation -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= $page_title ?? 'Dashboard' ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-bell"></i>
                                Benachrichtigungen
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-person-circle"></i>
                                Profil
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Content -->
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
