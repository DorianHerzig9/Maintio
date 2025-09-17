<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="bi bi-tools me-2"></i><?= $page_title ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Berichte</a></li>
                        <li class="breadcrumb-item active">Wartung</li>
                    </ol>
                </nav>
            </div>
            <div class="dropdown">
                <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i>Exportieren
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="alert('CSV Export in Entwicklung')">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="alert('PDF Export in Entwicklung')">
                        <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['total'] ?></div>
                        <div class="small">Wartungsaufgaben</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-gear display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['overdue'] ?></div>
                        <div class="small">Überfällig</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-exclamation-triangle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['upcoming'] ?></div>
                        <div class="small">Anstehend (30 Tage)</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-event display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['compliance_rate'] ?>%</div>
                        <div class="small">Compliance-Rate</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-funnel me-2"></i>Filter
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('reports/maintenance') ?>">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date_from" class="form-label">Von Datum</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                   value="<?= $filters['date_from'] ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="date_to" class="form-label">Bis Datum</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                   value="<?= $filters['date_to'] ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Wartungstyp</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Alle Typen</option>
                                <?php foreach ($maintenanceTypes as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $filters['type'] === $key ? 'selected' : '' ?>>
                                        <?= esc($value) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Filter anwenden
                            </button>
                            <a href="<?= base_url('reports/maintenance') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x me-1"></i>Zurücksetzen
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>Wartungsaufgaben (<?= count($maintenanceData) ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($maintenanceData)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Anlage</th>
                                    <th>Wartungstyp</th>
                                    <th>Beschreibung</th>
                                    <th>Nächster Termin</th>
                                    <th>Intervall</th>
                                    <th>Status</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($maintenanceData as $maintenance): ?>
                                <tr class="<?= getMaintenanceRowClass($maintenance['next_due_date']) ?>">
                                    <td>
                                        <strong><?= esc($maintenance['asset_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($maintenance['asset_number']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= getMaintenanceTypeText($maintenance['maintenance_type'] ?? 'preventive') ?>
                                        </span>
                                    </td>
                                    <td><?= esc($maintenance['description']) ?></td>
                                    <td>
                                        <?= date('d.m.Y', strtotime($maintenance['next_due_date'])) ?>
                                        <?php if (strtotime($maintenance['next_due_date']) < time()): ?>
                                            <span class="badge bg-danger ms-1">Überfällig</span>
                                        <?php elseif (strtotime($maintenance['next_due_date']) <= strtotime('+7 days')): ?>
                                            <span class="badge bg-warning ms-1">Bald fällig</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $maintenance['interval_days'] ?> Tage</td>
                                    <td>
                                        <span class="badge bg-<?= getMaintenanceStatusColor($maintenance['next_due_date']) ?>">
                                            <?= getMaintenanceStatusText($maintenance['next_due_date']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('preventive-maintenance/' . $maintenance['id']) ?>"
                                           class="btn btn-sm btn-outline-primary" title="Details">
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
                        <i class="bi bi-calendar-x display-1 text-muted"></i>
                        <h4 class="text-muted">Keine Wartungsaufgaben gefunden</h4>
                        <p class="text-muted">Für den ausgewählten Zeitraum wurden keine Wartungsaufgaben gefunden.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Maintenance report loaded');
});
</script>

<?php
// Helper functions für die View
function getMaintenanceRowClass($dueDate) {
    $dueTimestamp = strtotime($dueDate);
    $now = time();

    if ($dueTimestamp < $now) {
        return 'table-danger'; // Überfällig
    } elseif ($dueTimestamp <= strtotime('+7 days')) {
        return 'table-warning'; // Bald fällig
    }
    return '';
}

function getMaintenanceStatusColor($dueDate) {
    $dueTimestamp = strtotime($dueDate);
    $now = time();

    if ($dueTimestamp < $now) {
        return 'danger'; // Überfällig
    } elseif ($dueTimestamp <= strtotime('+30 days')) {
        return 'warning'; // Anstehend
    }
    return 'success'; // Planmäßig
}

function getMaintenanceStatusText($dueDate) {
    $dueTimestamp = strtotime($dueDate);
    $now = time();

    if ($dueTimestamp < $now) {
        return 'Überfällig';
    } elseif ($dueTimestamp <= strtotime('+30 days')) {
        return 'Anstehend';
    }
    return 'Planmäßig';
}

function getMaintenanceTypeText($type) {
    switch ($type) {
        case 'preventive': return 'Präventiv';
        case 'corrective': return 'Korrektiv';
        case 'predictive': return 'Prädiktiv';
        default: return ucfirst($type);
    }
}
?>

<?= $this->endSection() ?>