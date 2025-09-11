<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-8">
        <!-- Hauptdetails -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0"><?= esc($work_order['title']) ?></h5>
                    <small class="text-muted"><?= esc($work_order['work_order_number']) ?></small>
                </div>
                <div>
                    <span class="badge bg-<?= getStatusColor($work_order['status']) ?> fs-6">
                        <?= getStatusText($work_order['status']) ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Typ:</strong><br>
                        <span class="badge bg-<?= getTypeColor($work_order['type']) ?>">
                            <?= getTypeText($work_order['type']) ?>
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Priorität:</strong><br>
                        <span class="badge bg-<?= getPriorityColor($work_order['priority']) ?>">
                            <?= getPriorityText($work_order['priority']) ?>
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Erstellt:</strong><br>
                        <small><?= date('d.m.Y H:i', strtotime($work_order['created_at'])) ?></small>
                    </div>
                    <div class="col-md-3">
                        <strong>Aktualisiert:</strong><br>
                        <small><?= date('d.m.Y H:i', strtotime($work_order['updated_at'])) ?></small>
                    </div>
                </div>

                <?php if (!empty($work_order['description'])): ?>
                    <div class="mb-3">
                        <strong>Beschreibung:</strong>
                        <p class="mt-1"><?= nl2br(esc($work_order['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($work_order['notes'])): ?>
                    <div class="mb-3">
                        <strong>Notizen:</strong>
                        <p class="mt-1"><?= nl2br(esc($work_order['notes'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php if ($work_order['scheduled_date']): ?>
                        <div class="col-md-6 mb-2">
                            <strong>Geplant für:</strong><br>
                            <i class="bi bi-calendar-event me-1"></i>
                            <?= date('d.m.Y H:i', strtotime($work_order['scheduled_date'])) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($work_order['estimated_duration']): ?>
                        <div class="col-md-6 mb-2">
                            <strong>Geschätzte Dauer:</strong><br>
                            <i class="bi bi-clock me-1"></i>
                            <?= formatDuration($work_order['estimated_duration']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($work_order['started_at']): ?>
                        <div class="col-md-6 mb-2">
                            <strong>Gestartet:</strong><br>
                            <i class="bi bi-play-circle me-1"></i>
                            <?= date('d.m.Y H:i', strtotime($work_order['started_at'])) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($work_order['completed_at']): ?>
                        <div class="col-md-6 mb-2">
                            <strong>Abgeschlossen:</strong><br>
                            <i class="bi bi-check-circle me-1"></i>
                            <?= date('d.m.Y H:i', strtotime($work_order['completed_at'])) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($work_order['actual_duration']): ?>
                        <div class="col-md-6 mb-2">
                            <strong>Tatsächliche Dauer:</strong><br>
                            <i class="bi bi-stopwatch me-1"></i>
                            <?= formatDuration($work_order['actual_duration']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Status-Verlauf
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Arbeitsauftrag erstellt</h6>
                            <p class="timeline-text">
                                Erstellt von <strong><?= esc($created_by['first_name'] . ' ' . $created_by['last_name']) ?></strong>
                            </p>
                            <small class="text-muted"><?= date('d.m.Y H:i', strtotime($work_order['created_at'])) ?></small>
                        </div>
                    </div>

                    <?php if ($work_order['started_at']): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Bearbeitung gestartet</h6>
                                <small class="text-muted"><?= date('d.m.Y H:i', strtotime($work_order['started_at'])) ?></small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($work_order['completed_at']): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Arbeitsauftrag abgeschlossen</h6>
                                <small class="text-muted"><?= date('d.m.Y H:i', strtotime($work_order['completed_at'])) ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
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
                    <a href="<?= base_url('work-orders/' . $work_order['id'] . '/edit') ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Bearbeiten
                    </a>
                    
                    <?php if ($work_order['status'] === 'open'): ?>
                        <button class="btn btn-success" onclick="updateStatus('in_progress')">
                            <i class="bi bi-play me-1"></i>Starten
                        </button>
                    <?php elseif ($work_order['status'] === 'in_progress'): ?>
                        <button class="btn btn-success" onclick="updateStatus('completed')">
                            <i class="bi bi-check me-1"></i>Abschließen
                        </button>
                        <button class="btn btn-warning" onclick="updateStatus('on_hold')">
                            <i class="bi bi-pause me-1"></i>Pausieren
                        </button>
                    <?php elseif ($work_order['status'] === 'on_hold'): ?>
                        <button class="btn btn-info" onclick="updateStatus('in_progress')">
                            <i class="bi bi-play me-1"></i>Fortsetzen
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($work_order['status'] !== 'completed' && $work_order['status'] !== 'cancelled'): ?>
                        <button class="btn btn-danger" onclick="updateStatus('cancelled')">
                            <i class="bi bi-x-circle me-1"></i>Abbrechen
                        </button>
                    <?php endif; ?>
                    
                    <hr>
                    <a href="<?= base_url('work-orders') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Zurück zur Übersicht
                    </a>
                </div>
            </div>
        </div>

        <!-- Zugeordnete Anlage -->
        <?php if ($asset): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-cpu me-2"></i>
                        Zugeordnete Anlage
                    </h6>
                </div>
                <div class="card-body">
                    <h6><?= esc($asset['name']) ?></h6>
                    <p class="mb-1"><strong>Nummer:</strong> <?= esc($asset['asset_number']) ?></p>
                    <p class="mb-1"><strong>Typ:</strong> <?= esc($asset['type']) ?></p>
                    <p class="mb-1"><strong>Standort:</strong> <?= esc($asset['location']) ?></p>
                    <p class="mb-3">
                        <strong>Status:</strong> 
                        <span class="badge bg-<?= getAssetStatusColor($asset['status']) ?>">
                            <?= getAssetStatusText($asset['status']) ?>
                        </span>
                    </p>
                    <a href="<?= base_url('assets/' . $asset['id']) ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i>Anlage anzeigen
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Zugewiesener Benutzer -->
        <?php if ($assigned_user): ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>
                        Zugewiesener Techniker
                    </h6>
                </div>
                <div class="card-body">
                    <h6><?= esc($assigned_user['first_name'] . ' ' . $assigned_user['last_name']) ?></h6>
                    <p class="mb-1"><strong>Benutzername:</strong> <?= esc($assigned_user['username']) ?></p>
                    <p class="mb-1"><strong>E-Mail:</strong> <?= esc($assigned_user['email']) ?></p>
                    <p class="mb-0">
                        <strong>Rolle:</strong> 
                        <span class="badge bg-info"><?= ucfirst($assigned_user['role']) ?></span>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>
                        Zuweisung
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Noch kein Techniker zugewiesen</p>
                    <a href="<?= base_url('work-orders/' . $work_order['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-person-plus me-1"></i>Techniker zuweisen
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    margin-left: 10px;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.timeline-text {
    margin-bottom: 5px;
    font-size: 0.85rem;
}
</style>

<script>
function updateStatus(newStatus) {
    const statusTexts = {
        'in_progress': 'in Bearbeitung setzen',
        'completed': 'abschließen',
        'on_hold': 'pausieren',
        'cancelled': 'abbrechen'
    };
    
    if (confirm(`Möchten Sie diesen Arbeitsauftrag wirklich ${statusTexts[newStatus]}?`)) {
        fetch(`<?= base_url('work-orders/' . $work_order['id']) ?>/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `status=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Fehler: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ein Fehler ist aufgetreten');
        });
    }
}
</script>
<?= $this->endSection() ?>

<?php
// Helper functions
function formatDuration($minutes) {
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    
    if ($hours > 0) {
        return $hours . 'h ' . $mins . 'min';
    } else {
        return $mins . ' Minuten';
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

function getTypeColor($type) {
    switch ($type) {
        case 'preventive': return 'info';
        case 'corrective': return 'warning';
        case 'emergency': return 'danger';
        case 'inspection': return 'secondary';
        default: return 'secondary';
    }
}

function getTypeText($type) {
    switch ($type) {
        case 'preventive': return 'Vorbeugend';
        case 'corrective': return 'Korrektiv';
        case 'emergency': return 'Notfall';
        case 'inspection': return 'Inspektion';
        default: return ucfirst($type);
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
        case 'maintenance': return 'Wartung';
        case 'out_of_order': return 'Außer Betrieb';
        case 'decommissioned': return 'Stillgelegt';
        default: return ucfirst($status);
    }
}
?>
