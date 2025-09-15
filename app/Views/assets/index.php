<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Filter und Suche -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Alle Status</option>
                            <option value="operational" <?= $status_filter === 'operational' ? 'selected' : '' ?>>Betriebsbereit</option>
                            <option value="maintenance" <?= $status_filter === 'maintenance' ? 'selected' : '' ?>>Instandhaltung</option>
                            <option value="out_of_order" <?= $status_filter === 'out_of_order' ? 'selected' : '' ?>>Außer Betrieb</option>
                            <option value="decommissioned" <?= $status_filter === 'decommissioned' ? 'selected' : '' ?>>Stillgelegt</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="priority" class="form-label">Priorität</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="">Alle Prioritäten</option>
                            <option value="low" <?= $priority_filter === 'low' ? 'selected' : '' ?>>Niedrig</option>
                            <option value="medium" <?= $priority_filter === 'medium' ? 'selected' : '' ?>>Mittel</option>
                            <option value="high" <?= $priority_filter === 'high' ? 'selected' : '' ?>>Hoch</option>
                            <option value="critical" <?= $priority_filter === 'critical' ? 'selected' : '' ?>>Kritisch</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Suche</label>
                        <input type="text" class="form-control" id="search" placeholder="Name, Nummer, Typ, Standort...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Filtern
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Aktionen -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-cpu me-2"></i>
                Anlagen (<?= count($assets) ?>)
            </h5>
            <a href="<?= base_url('assets/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Neue Anlage
            </a>
        </div>
    </div>
</div>

<!-- Assets Grid -->
<div class="row">
    <?php if (!empty($assets)): ?>
        <?php foreach ($assets as $asset): ?>
            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0"><?= esc($asset['name']) ?></h6>
                        <span class="badge bg-<?= getAssetStatusColor($asset['status']) ?>">
                            <?= getAssetStatusText($asset['status']) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Nummer:</small><br>
                                <strong><?= esc($asset['asset_number']) ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Typ:</small><br>
                                <?= esc($asset['type']) ?>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-12">
                                <small class="text-muted">Standort:</small><br>
                                <i class="bi bi-geo-alt me-1"></i><?= esc($asset['location']) ?>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Priorität:</small><br>
                                <span class="badge bg-<?= getPriorityColor($asset['priority']) ?>">
                                    <?= getPriorityText($asset['priority']) ?>
                                </span>
                            </div>
                            <?php if ($asset['installation_date']): ?>
                                <div class="col-6">
                                    <small class="text-muted">Installation:</small><br>
                                    <?= date('d.m.Y', strtotime($asset['installation_date'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($asset['manufacturer'] || $asset['model']): ?>
                            <div class="row mb-2">
                                <?php if ($asset['manufacturer']): ?>
                                    <div class="col-6">
                                        <small class="text-muted">Hersteller:</small><br>
                                        <?= esc($asset['manufacturer']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($asset['model']): ?>
                                    <div class="col-6">
                                        <small class="text-muted">Modell:</small><br>
                                        <?= esc($asset['model']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($asset['description'])): ?>
                            <div class="mb-2">
                                <small class="text-muted">Beschreibung:</small><br>
                                <small><?= esc(substr($asset['description'], 0, 100)) ?><?= strlen($asset['description']) > 100 ? '...' : '' ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100" role="group">
                            <a href="<?= base_url('assets/' . $asset['id']) ?>" 
                               class="btn btn-outline-primary btn-sm" title="Anzeigen">
                                <i class="bi bi-eye me-1"></i>Details
                            </a>
                            <a href="<?= base_url('assets/' . $asset['id'] . '/edit') ?>" 
                               class="btn btn-outline-secondary btn-sm" title="Bearbeiten">
                                <i class="bi bi-pencil me-1"></i>Bearbeiten
                            </a>
                            <a href="<?= base_url('work-orders/create?asset_id=' . $asset['id']) ?>" 
                               class="btn btn-outline-success btn-sm" title="Arbeitsauftrag erstellen">
                                <i class="bi bi-plus me-1"></i>Auftrag
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-cpu display-1 text-muted"></i>
                    <h5 class="mt-3">Keine Anlagen gefunden</h5>
                    <p class="text-muted">Erstellen Sie Ihre erste Anlage oder passen Sie die Filter an.</p>
                    <a href="<?= base_url('assets/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Anlage erstellen
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Statistiken -->
<?php if (!empty($assets)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>
                        Anlagen-Übersicht
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <?php
                        $statusCounts = array_count_values(array_column($assets, 'status'));
                        $priorityCounts = array_count_values(array_column($assets, 'priority'));
                        ?>
                        
                        <div class="col-md-3">
                            <h6>Status</h6>
                            <?php foreach ($statusCounts as $status => $count): ?>
                                <span class="badge bg-<?= getAssetStatusColor($status) ?> me-1">
                                    <?= getAssetStatusText($status) ?>: <?= $count ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="col-md-3">
                            <h6>Priorität</h6>
                            <?php foreach ($priorityCounts as $priority => $count): ?>
                                <span class="badge bg-<?= getPriorityColor($priority) ?> me-1">
                                    <?= getPriorityText($priority) ?>: <?= $count ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="col-md-3">
                            <h6>Gesamt</h6>
                            <span class="badge bg-info fs-6"><?= count($assets) ?> Anlagen</span>
                        </div>
                        
                        <div class="col-md-3">
                            <h6>Kritische</h6>
                            <span class="badge bg-danger fs-6">
                                <?= $priorityCounts['critical'] ?? 0 ?> Kritisch
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Live-Suche
document.getElementById('search').addEventListener('input', function() {
    const query = this.value;
    if (query.length > 2) {
        // Hier könnte eine AJAX-Suche implementiert werden
        console.log('Suche nach:', query);
    }
});
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
?>
