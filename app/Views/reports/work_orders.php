<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-clipboard-data me-2"></i>Arbeitsaufträge Bericht</h2>
            <div>
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i>Exportieren
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('reports/export-work-orders?format=csv&' . http_build_query($filters)) ?>">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV
                        </a></li>
                        <li><a class="dropdown-item" href="<?= base_url('reports/export-work-orders?format=pdf&' . http_build_query($filters)) ?>">
                            <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="alert('Excel Export in Entwicklung')">
                            <i class="bi bi-file-earmark-excel me-2"></i>Excel
                        </a></li>
                    </ul>
                </div>
                <a href="<?= base_url('reports') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Zurück zu Berichten
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-funnel me-2"></i>Filter & Optionen
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('reports/work-orders') ?>">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="date_from" class="form-label">Von Datum</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                   value="<?= esc($filters['date_from']) ?>">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="date_to" class="form-label">Bis Datum</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                   value="<?= esc($filters['date_to']) ?>">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Alle Status</option>
                                <?php foreach ($statusOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="technician" class="form-label">Techniker</label>
                            <select class="form-select" id="technician" name="technician">
                                <option value="">Alle Techniker</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>" <?= $filters['technician'] == $tech['id'] ? 'selected' : '' ?>>
                                        <?= esc($tech['first_name'] . ' ' . $tech['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Filter anwenden
                        </button>
                        <a href="<?= base_url('reports/work-orders') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Filter zurücksetzen
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['total'] ?></div>
                        <div class="small">Gesamt Aufträge</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clipboard-check display-6"></i>
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
                        <div class="h5 mb-0"><?= $stats['completed'] ?></div>
                        <div class="small">Abgeschlossen</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle display-6"></i>
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
                        <div class="h5 mb-0"><?= $stats['in_progress'] ?></div>
                        <div class="small">In Bearbeitung</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-hourglass-split display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['completion_rate'] ?>%</div>
                        <div class="small">Abschlussrate</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-percent display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Work Orders Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>Arbeitsaufträge Details
                    <span class="badge bg-primary ms-2"><?= count($workOrders) ?> Einträge</span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($workOrders)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="workOrdersTable">
                            <thead>
                                <tr>
                                    <th>Auftragsnummer</th>
                                    <th>Titel</th>
                                    <th>Anlage</th>
                                    <th>Techniker</th>
                                    <th>Status</th>
                                    <th>Priorität</th>
                                    <th>Erstellt</th>
                                    <th>Fällig</th>
                                    <th>Abgeschlossen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($workOrders as $order): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('work-orders/' . $order['id']) ?>" class="text-decoration-none">
                                            <?= esc($order['work_order_number']) ?>
                                        </a>
                                    </td>
                                    <td><?= esc($order['title']) ?></td>
                                    <td>
                                        <?php if ($order['asset_name']): ?>
                                            <div>
                                                <strong><?= esc($order['asset_name']) ?></strong><br>
                                                <small class="text-muted"><?= esc($order['asset_number']) ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Keine Anlage</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($order['first_name'] && $order['last_name']): ?>
                                            <?= esc($order['first_name'] . ' ' . $order['last_name']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Nicht zugewiesen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= getWorkOrderStatusColor($order['status']) ?>">
                                            <?= getWorkOrderStatusText($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= getWorkOrderPriorityColor($order['priority'] ?? 'medium') ?>">
                                            <?= getWorkOrderPriorityText($order['priority'] ?? 'medium') ?>
                                        </span>
                                    </td>
                                    <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <?php if (isset($order['scheduled_date']) && $order['scheduled_date']): ?>
                                            <span class="<?= strtotime($order['scheduled_date']) < time() && $order['status'] !== 'completed' ? 'text-danger' : '' ?>">
                                                <?= date('d.m.Y', strtotime($order['scheduled_date'])) ?>
                                                <?php if (strtotime($order['scheduled_date']) < time() && $order['status'] !== 'completed'): ?>
                                                    <i class="bi bi-exclamation-triangle text-danger ms-1" title="Überfällig"></i>
                                                <?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($order['completed_at']) && $order['completed_at']): ?>
                                            <?= date('d.m.Y H:i', strtotime($order['completed_at'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">Keine Arbeitsaufträge gefunden</h5>
                        <p class="text-muted">Passen Sie Ihre Filter an oder erstellen Sie neue Arbeitsaufträge.</p>
                        <a href="<?= base_url('work-orders/create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Neuen Auftrag erstellen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Summary Information -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h6><i class="bi bi-info-circle me-2"></i>Berichtszusammenfassung</h6>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <strong>Zeitraum:</strong> <?= date('d.m.Y', strtotime($filters['date_from'])) ?> - <?= date('d.m.Y', strtotime($filters['date_to'])) ?><br>
                            <strong>Gefiltert nach:</strong>
                            <?php
                            $filterInfo = [];
                            if ($filters['status']) $filterInfo[] = 'Status: ' . $statusOptions[$filters['status']];
                            if ($filters['technician']) {
                                $techName = '';
                                foreach ($technicians as $tech) {
                                    if ($tech['id'] == $filters['technician']) {
                                        $techName = $tech['first_name'] . ' ' . $tech['last_name'];
                                        break;
                                    }
                                }
                                $filterInfo[] = 'Techniker: ' . $techName;
                            }
                            echo !empty($filterInfo) ? implode(', ', $filterInfo) : 'Keine Filter';
                            ?>
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">
                            <strong>Erstellt am:</strong> <?= date('d.m.Y H:i') ?><br>
                            <strong>Anzahl Einträge:</strong> <?= count($workOrders) ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable if available
    if (typeof DataTable !== 'undefined' && document.getElementById('workOrdersTable')) {
        new DataTable('#workOrdersTable', {
            responsive: true,
            pageLength: 25,
            order: [[6, 'desc']], // Sort by created date
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/de-DE.json'
            },
            columnDefs: [
                { orderable: false, targets: [1, 2] } // Disable ordering for title and asset columns
            ]
        });
    }

    // Auto-submit form when date changes
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Optional: Auto-submit when date changes
            // this.form.submit();
        });
    });
});

// Helper functions for status and priority styling
function getWorkOrderStatusColor(status) {
    switch(status) {
        case 'open': return 'primary';
        case 'in_progress': return 'warning';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function getWorkOrderStatusText(status) {
    switch(status) {
        case 'open': return 'Offen';
        case 'in_progress': return 'In Bearbeitung';
        case 'completed': return 'Abgeschlossen';
        case 'cancelled': return 'Storniert';
        default: return status;
    }
}

function getWorkOrderPriorityColor(priority) {
    switch(priority) {
        case 'low': return 'success';
        case 'medium': return 'warning';
        case 'high': return 'danger';
        case 'urgent': return 'dark';
        default: return 'secondary';
    }
}

function getWorkOrderPriorityText(priority) {
    switch(priority) {
        case 'low': return 'Niedrig';
        case 'medium': return 'Mittel';
        case 'high': return 'Hoch';
        case 'urgent': return 'Dringend';
        default: return priority;
    }
}
</script>
<?= $this->endSection() ?>

<?php
function getWorkOrderStatusColor($status) {
    switch($status) {
        case 'open': return 'primary';
        case 'in_progress': return 'warning';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function getWorkOrderStatusText($status) {
    switch($status) {
        case 'open': return 'Offen';
        case 'in_progress': return 'In Bearbeitung';
        case 'completed': return 'Abgeschlossen';
        case 'cancelled': return 'Storniert';
        default: return $status;
    }
}

function getWorkOrderPriorityColor($priority) {
    switch($priority) {
        case 'low': return 'success';
        case 'medium': return 'warning';
        case 'high': return 'danger';
        case 'urgent': return 'dark';
        default: return 'secondary';
    }
}

function getWorkOrderPriorityText($priority) {
    switch($priority) {
        case 'low': return 'Niedrig';
        case 'medium': return 'Mittel';
        case 'high': return 'Hoch';
        case 'urgent': return 'Dringend';
        default: return $priority;
    }
}
?>