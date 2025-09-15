<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-8">
        <!-- Hauptdetails -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0"><?= esc($asset['name']) ?></h5>
                    <small class="text-muted"><?= esc($asset['asset_number']) ?></small>
                </div>
                <div>
                    <span class="badge bg-<?= getAssetStatusColor($asset['status']) ?> fs-6">
                        <?= getAssetStatusText($asset['status']) ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Typ:</strong><br>
                        <span class="badge bg-info"><?= esc($asset['type']) ?></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Priorität:</strong><br>
                        <span class="badge bg-<?= getPriorityColor($asset['priority']) ?>">
                            <?= getPriorityText($asset['priority']) ?>
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Erstellt:</strong><br>
                        <small><?= date('d.m.Y H:i', strtotime($asset['created_at'])) ?></small>
                    </div>
                    <div class="col-md-3">
                        <strong>Aktualisiert:</strong><br>
                        <small><?= date('d.m.Y H:i', strtotime($asset['updated_at'])) ?></small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Standort:</strong><br>
                        <i class="bi bi-geo-alt me-1"></i><?= esc($asset['location']) ?>
                    </div>
                    <?php if ($asset['installation_date']): ?>
                        <div class="col-md-6">
                            <strong>Installation:</strong><br>
                            <i class="bi bi-calendar me-1"></i>
                            <?= date('d.m.Y', strtotime($asset['installation_date'])) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($asset['manufacturer'] || $asset['model'] || $asset['serial_number']): ?>
                    <div class="row mb-3">
                        <?php if ($asset['manufacturer']): ?>
                            <div class="col-md-4">
                                <strong>Hersteller:</strong><br>
                                <?= esc($asset['manufacturer']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($asset['model']): ?>
                            <div class="col-md-4">
                                <strong>Modell:</strong><br>
                                <?= esc($asset['model']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($asset['serial_number']): ?>
                            <div class="col-md-4">
                                <strong>Seriennummer:</strong><br>
                                <?= esc($asset['serial_number']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($asset['description'])): ?>
                    <div class="mb-3">
                        <strong>Beschreibung:</strong>
                        <p class="mt-1"><?= nl2br(esc($asset['description'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Arbeitsaufträge -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clipboard-check me-2"></i>
                    Arbeitsaufträge (<?= count($work_orders) ?>)
                </h6>
                <a href="<?= base_url('work-orders/create?asset_id=' . $asset['id']) ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus me-1"></i>Neuer Auftrag
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($work_orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nummer</th>
                                    <th>Titel</th>
                                    <th>Status</th>
                                    <th>Priorität</th>
                                    <th>Erstellt</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($work_orders as $order): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('work-orders/' . $order['id']) ?>" class="text-decoration-none">
                                                <?= esc($order['work_order_number']) ?>
                                            </a>
                                        </td>
                                        <td><?= esc($order['title']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= getStatusColor($order['status']) ?>">
                                                <?= getStatusText($order['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= getPriorityColor($order['priority']) ?>">
                                                <?= getPriorityText($order['priority']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= base_url('work-orders/' . $order['id']) ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-clipboard-x display-6 text-muted"></i>
                        <p class="mt-2 text-muted">Noch keine Arbeitsaufträge für diese Anlage</p>
                        <a href="<?= base_url('work-orders/create?asset_id=' . $asset['id']) ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus me-1"></i>Ersten Auftrag erstellen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Aktionen -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Aktionen
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('assets/' . $asset['id'] . '/edit') ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Bearbeiten
                    </a>
                    
                    <a href="<?= base_url('work-orders/create?asset_id=' . $asset['id']) ?>" class="btn btn-success">
                        <i class="bi bi-plus me-1"></i>Arbeitsauftrag erstellen
                    </a>
                    
                    <hr>
                    
                    <a href="<?= base_url('assets') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Zurück zur Übersicht
                    </a>
                    
                    <button class="btn btn-outline-danger" onclick="confirmDelete()">
                        <i class="bi bi-trash me-1"></i>Löschen
                    </button>
                </div>
            </div>
        </div>

        <!-- Anlagen-Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Status-Informationen
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Aktueller Status:</strong><br>
                    <span class="badge bg-<?= getAssetStatusColor($asset['status']) ?> fs-6">
                        <?= getAssetStatusText($asset['status']) ?>
                    </span>
                </div>
                
                <div class="mb-3">
                    <strong>Priorität:</strong><br>
                    <span class="badge bg-<?= getPriorityColor($asset['priority']) ?> fs-6">
                        <?= getPriorityText($asset['priority']) ?>
                    </span>
                </div>

                <?php
                $openOrders = array_filter($work_orders, function($order) {
                    return $order['status'] === 'open' || $order['status'] === 'in_progress';
                });
                ?>
                
                <div class="mb-3">
                    <strong>Offene Aufträge:</strong><br>
                    <span class="badge bg-<?= count($openOrders) > 0 ? 'warning' : 'success' ?> fs-6">
                        <?= count($openOrders) ?>
                    </span>
                </div>

                <?php if ($asset['installation_date']): ?>
                    <div class="mb-3">
                        <strong>Betriebszeit:</strong><br>
                        <?php
                        $installDate = new DateTime($asset['installation_date']);
                        $now = new DateTime();
                        $diff = $now->diff($installDate);
                        echo $diff->y . ' Jahre, ' . $diff->m . ' Monate';
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>
                    Arbeitsaufträge Statistik
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($work_orders)): ?>
                    <?php
                    $statusCounts = array_count_values(array_column($work_orders, 'status'));
                    $totalOrders = count($work_orders);
                    $completedOrders = $statusCounts['completed'] ?? 0;
                    $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;
                    ?>
                    
                    <div class="mb-2">
                        <small class="text-muted">Gesamt:</small>
                        <strong><?= $totalOrders ?></strong>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Abgeschlossen:</small>
                        <strong><?= $completedOrders ?></strong>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Offen:</small>
                        <strong><?= ($statusCounts['open'] ?? 0) + ($statusCounts['in_progress'] ?? 0) ?></strong>
                    </div>
                    
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?= $completionRate ?>%" 
                             aria-valuenow="<?= $completionRate ?>" aria-valuemin="0" aria-valuemax="100">
                            <?= $completionRate ?>%
                        </div>
                    </div>
                    <small class="text-muted">Abschlussrate</small>
                <?php else: ?>
                    <p class="text-muted mb-0">Noch keine Arbeitsaufträge vorhanden</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete() {
    if (confirm('Möchten Sie diese Anlage wirklich löschen?\n\nHinweis: Anlagen mit zugeordneten Arbeitsaufträgen können nicht gelöscht werden.')) {
        // Erstelle und sende DELETE-Request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('assets/' . $asset['id']) ?>';
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        
        form.appendChild(methodInput);
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>

<?php
// Helper functions
function getAssetStatusColor($status) {
    switch ($status) {
        case 'operational': return 'success';
        case 'maintenance': return 'warning';
        case 'out_of_order': return 'danger';
        case 'decommissioned': return 'secondary';
        default: return 'secondary';
    }
}

function getAssetStatusText($status) {
    switch ($status) {
        case 'operational': return 'Betriebsbereit';
        case 'maintenance': return 'Instandhaltung';
        case 'out_of_order': return 'Außer Betrieb';
        case 'decommissioned': return 'Stillgelegt';
        default: return ucfirst($status);
    }
}

function getPriorityColor($priority) {
    switch ($priority) {
        case 'low': return 'secondary';
        case 'medium': return 'info';
        case 'high': return 'warning';
        case 'critical': return 'danger';
        default: return 'secondary';
    }
}

function getPriorityText($priority) {
    switch ($priority) {
        case 'low': return 'Niedrig';
        case 'medium': return 'Mittel';
        case 'high': return 'Hoch';
        case 'critical': return 'Kritisch';
        default: return ucfirst($priority);
    }
}

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
?>
