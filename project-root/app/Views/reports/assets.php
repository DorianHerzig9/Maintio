<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Berichte</a></li>
                        <li class="breadcrumb-item active">Anlagen</li>
                    </ol>
                </nav>
            </div>
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i>Exportieren
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= base_url('reports/export-assets?format=csv') ?>">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV
                    </a></li>
                    <li><a class="dropdown-item" href="<?= base_url('reports/export-assets?format=pdf&' . http_build_query($filters)) ?>">
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
                        <div class="small">Gesamt Anlagen</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-cpu display-6"></i>
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
                        <div class="h5 mb-0"><?= $stats['operational'] ?></div>
                        <div class="small">Betriebsbereit</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle display-6"></i>
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
                        <div class="h5 mb-0"><?= $stats['maintenance'] ?></div>
                        <div class="small">In Wartung</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-tools display-6"></i>
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
                        <div class="h5 mb-0"><?= $stats['out_of_service'] ?></div>
                        <div class="small">Außer Betrieb</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-x-circle display-6"></i>
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
                <form method="GET" action="<?= base_url('reports/assets') ?>">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Alle Status</option>
                                <option value="operational" <?= $filters['status'] === 'operational' ? 'selected' : '' ?>>Betriebsbereit</option>
                                <option value="maintenance" <?= $filters['status'] === 'maintenance' ? 'selected' : '' ?>>In Wartung</option>
                                <option value="out_of_order" <?= $filters['status'] === 'out_of_order' ? 'selected' : '' ?>>Defekt</option>
                                <option value="decommissioned" <?= $filters['status'] === 'decommissioned' ? 'selected' : '' ?>>Stillgelegt</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Typ</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Alle Typen</option>
                                <?php foreach ($assetTypes as $assetType): ?>
                                    <option value="<?= $assetType['type'] ?>" <?= $filters['type'] === $assetType['type'] ? 'selected' : '' ?>>
                                        <?= esc($assetType['type']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="location" class="form-label">Standort</label>
                            <select class="form-select" id="location" name="location">
                                <option value="">Alle Standorte</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= $loc['location'] ?>" <?= $filters['location'] === $loc['location'] ? 'selected' : '' ?>>
                                        <?= esc($loc['location']) ?>
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
                            <a href="<?= base_url('reports/assets') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x me-1"></i>Zurücksetzen
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Assets Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>Anlagen (<?= count($assets) ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($assets)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Anlage</th>
                                    <th>Typ</th>
                                    <th>Standort</th>
                                    <th>Status</th>
                                    <th>Priorität</th>
                                    <th>Hersteller</th>
                                    <th>Installiert</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assets as $asset): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($asset['name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($asset['asset_number']) ?></small>
                                    </td>
                                    <td><?= esc($asset['type']) ?></td>
                                    <td><?= esc($asset['location']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= getAssetStatusColor($asset['status'] ?? 'unknown') ?>">
                                            <?= getAssetStatusText($asset['status'] ?? 'unknown') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= getPriorityColor($asset['priority'] ?? 'medium') ?>">
                                            <?= getPriorityText($asset['priority'] ?? 'medium') ?>
                                        </span>
                                    </td>
                                    <td><?= esc($asset['manufacturer'] ?? '-') ?></td>
                                    <td><?= $asset['installation_date'] ? date('d.m.Y', strtotime($asset['installation_date'])) : '-' ?></td>
                                    <td>
                                        <a href="<?= base_url('assets/' . $asset['id']) ?>" class="btn btn-sm btn-outline-primary" title="Details">
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
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="text-muted">Keine Anlagen gefunden</h4>
                        <p class="text-muted">Mit den aktuellen Filtern wurden keine Anlagen gefunden.</p>
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
    console.log('Assets report loaded');
});

// Helper functions
function getAssetStatusColor(status) {
    switch (status) {
        case 'operational': return 'success';
        case 'maintenance': return 'warning';
        case 'out_of_order': return 'danger';
        case 'decommissioned': return 'secondary';
        default: return 'secondary';
    }
}

function getAssetStatusText(status) {
    switch (status) {
        case 'operational': return 'Betriebsbereit';
        case 'maintenance': return 'In Wartung';
        case 'out_of_order': return 'Defekt';
        case 'decommissioned': return 'Stillgelegt';
        default: return status;
    }
}

function getPriorityColor(priority) {
    switch (priority) {
        case 'low': return 'secondary';
        case 'medium': return 'primary';
        case 'high': return 'warning';
        case 'critical': return 'danger';
        default: return 'secondary';
    }
}

function getPriorityText(priority) {
    switch (priority) {
        case 'low': return 'Niedrig';
        case 'medium': return 'Mittel';
        case 'high': return 'Hoch';
        case 'critical': return 'Kritisch';
        default: return priority;
    }
}
</script>

<?php
// Helper functions für die View
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
        case 'maintenance': return 'In Wartung';
        case 'out_of_order': return 'Defekt';
        case 'decommissioned': return 'Stillgelegt';
        default: return ucfirst($status);
    }
}

function getPriorityColor($priority) {
    switch ($priority) {
        case 'low': return 'secondary';
        case 'medium': return 'primary';
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
?>

<?= $this->endSection() ?>