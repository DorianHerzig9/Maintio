<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['total_work_orders'] ?></div>
                        <div class="small">Gesamt Arbeitsaufträge</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clipboard-check display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['open_work_orders'] ?></div>
                        <div class="small">Offene Aufträge</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-exclamation-triangle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['completion_rate'] ?>%</div>
                        <div class="small">Abschlussrate</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['critical_work_orders'] ?></div>
                        <div class="small">Kritische Aufträge</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-exclamation-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Data -->
<div class="row mb-4">
    <!-- Work Order Status Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Arbeitsaufträge nach Status
                </h5>
            </div>
            <div class="card-body">
                <canvas id="workOrderStatusChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Asset Status Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>
                    Anlagen nach Status
                </h5>
            </div>
            <div class="card-body">
                <canvas id="assetStatusChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Schnellaktionen
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="<?= base_url('work-orders/create') ?>" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i>
                            Neuer Arbeitsauftrag
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= base_url('assets/create') ?>" class="btn btn-success w-100">
                            <i class="bi bi-plus-square me-2"></i>
                            Neue Anlage
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-info w-100" onclick="refreshDashboard()">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Dashboard aktualisieren
                        </button>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="#" class="btn btn-warning w-100">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Bericht erstellen
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <!-- Recent Work Orders -->
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Neueste Arbeitsaufträge
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_work_orders)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_work_orders as $order): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold"><?= esc($order['title']) ?></div>
                                    <small class="text-muted">
                                        <?= esc($order['work_order_number']) ?> • 
                                        <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                    </small>
                                </div>
                                <span class="badge bg-<?= getStatusColor($order['status']) ?> rounded-pill">
                                    <?= getStatusText($order['status']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= base_url('work-orders') ?>" class="btn btn-outline-primary btn-sm">
                            Alle anzeigen
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-6"></i>
                        <p class="mt-2">Noch keine Arbeitsaufträge vorhanden</p>
                        <a href="<?= base_url('work-orders/create') ?>" class="btn btn-primary btn-sm">
                            Ersten Auftrag erstellen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Critical Assets -->
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Kritische Anlagen
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($critical_assets)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($critical_assets as $asset): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold"><?= esc($asset['name']) ?></div>
                                    <small class="text-muted">
                                        <?= esc($asset['asset_number']) ?> • 
                                        <?= esc($asset['location']) ?>
                                    </small>
                                </div>
                                <span class="badge bg-danger rounded-pill">Kritisch</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= base_url('assets') ?>" class="btn btn-outline-danger btn-sm">
                            Alle kritischen anzeigen
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle display-6 text-success"></i>
                        <p class="mt-2">Keine kritischen Anlagen</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Work Order Status Chart
const workOrderCtx = document.getElementById('workOrderStatusChart').getContext('2d');
const workOrderChart = new Chart(workOrderCtx, {
    type: 'doughnut',
    data: {
        labels: [
            <?php foreach ($stats['work_order_stats']['by_status'] as $status): ?>
                '<?= getStatusText($status['status']) ?>',
            <?php endforeach; ?>
        ],
        datasets: [{
            data: [
                <?php foreach ($stats['work_order_stats']['by_status'] as $status): ?>
                    <?= $status['count'] ?>,
                <?php endforeach; ?>
            ],
            backgroundColor: [
                '#fbbf24', // open
                '#3b82f6', // in_progress
                '#10b981', // completed
                '#ef4444', // cancelled
                '#6b7280'  // on_hold
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Asset Status Chart
const assetCtx = document.getElementById('assetStatusChart').getContext('2d');
const assetChart = new Chart(assetCtx, {
    type: 'bar',
    data: {
        labels: [
            <?php foreach ($stats['asset_stats']['by_status'] as $status): ?>
                '<?= getAssetStatusText($status['status']) ?>',
            <?php endforeach; ?>
        ],
        datasets: [{
            label: 'Anzahl Anlagen',
            data: [
                <?php foreach ($stats['asset_stats']['by_status'] as $status): ?>
                    <?= $status['count'] ?>,
                <?php endforeach; ?>
            ],
            backgroundColor: [
                '#10b981', // operational
                '#fbbf24', // maintenance
                '#ef4444', // out_of_order
                '#6b7280'  // decommissioned
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function refreshDashboard() {
    fetch('<?= base_url('api/dashboard/stats') ?>')
        .then(response => response.json())
        .then(data => {
            // Update stats cards
            location.reload(); // Einfache Lösung für jetzt
        })
        .catch(error => {
            console.error('Fehler beim Aktualisieren:', error);
        });
}
</script>
<?= $this->endSection() ?>

<?php
// Helper functions
function getStatusColor($status) {
    switch ($status) {
        case 'open': return 'warning';
        case 'in_progress': return 'primary';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        case 'on_hold': return 'secondary';
        default: return 'secondary';
    }
}

function getStatusText($status) {
    switch ($status) {
        case 'open': return 'Offen';
        case 'in_progress': return 'In Bearbeitung';
        case 'completed': return 'Abgeschlossen';
        case 'cancelled': return 'Abgebrochen';
        case 'on_hold': return 'Wartend';
        default: return ucfirst($status);
    }
}

function getAssetStatusText($status) {
    switch ($status) {
        case 'operational': return 'Betriebsbereit';
        case 'maintenance': return 'Wartung';
        case 'out_of_order': return 'Außer Betrieb';
        case 'decommissioned': return 'Stillgelegt';
        default: return ucfirst($status);
    }
}
?>
