<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted">Verwaltung von Instandhaltungsplänen</p>
    </div>
    <div>
        <button class="btn btn-success me-2" onclick="generateWorkOrders()">
            <i class="bi bi-gear-wide-connected me-1"></i>
            Arbeitsaufträge generieren
        </button>
        <a href="<?= base_url('preventive-maintenance/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Neuer Instandhaltungsplan
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['total_active'] ?></div>
                        <div class="small">Aktive Instandhaltungspläne</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-check display-6 text-primary"></i>
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
                        <div class="small">Überfällige Instandhaltungen</div>
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
                        <div class="h5 mb-0"><?= $stats['upcoming_30_days'] ?></div>
                        <div class="small">Anstehend (30 Tage)</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clock display-6"></i>
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
                        <div class="h5 mb-0">
                            <?php 
                            $criticalCount = 0;
                            foreach($stats['by_priority'] as $priority) {
                                if($priority['priority'] === 'critical') $criticalCount = $priority['count'];
                            }
                            echo $criticalCount;
                            ?>
                        </div>
                        <div class="small">Kritische Priorität</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-exclamation-octagon display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Suche</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?= esc($search) ?>" placeholder="Name, Beschreibung, Anlage...">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status Filter</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Alle anzeigen</option>
                    <option value="overdue" <?= $status === 'overdue' ? 'selected' : '' ?>>Überfällig</option>
                    <option value="upcoming" <?= $status === 'upcoming' ? 'selected' : '' ?>>Anstehend (30 Tage)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i>
                        Filtern
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="<?= base_url('preventive-maintenance') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Zurücksetzen
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Maintenance Schedules Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-list-ul me-2"></i>
            Instandhaltungspläne
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($schedules)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Anlage</th>
                            <th>Intervall</th>
                            <th>Priorität</th>
                            <th>Nächste Instandhaltung</th>
                            <th>Status</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <?php 
                            $nextDue = new DateTime($schedule['next_due']);
                            $today = new DateTime();
                            $isOverdue = $nextDue < $today;
                            $daysUntil = $today->diff($nextDue)->format('%R%a');
                            ?>
                            <tr class="<?= $isOverdue ? 'table-danger' : ($daysUntil <= 7 && $daysUntil >= 0 ? 'table-warning' : '') ?>">
                                <td>
                                    <div class="fw-bold"><?= esc($schedule['schedule_name']) ?></div>
                                    <?php if (!empty($schedule['category'])): ?>
                                        <small class="text-muted">
                                            <i class="bi bi-tag me-1"></i><?= esc($schedule['category']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><?= esc($schedule['asset_name']) ?></div>
                                    <small class="text-muted"><?= esc($schedule['asset_number']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= $schedule['interval_value'] ?> 
                                        <?= App\Models\PreventiveMaintenanceModel::getIntervalTypeText($schedule['interval_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= App\Models\PreventiveMaintenanceModel::getPriorityColor($schedule['priority']) ?>">
                                        <?= ucfirst($schedule['priority']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div><?= $nextDue->format('d.m.Y H:i') ?></div>
                                    <?php if ($isOverdue): ?>
                                        <small class="text-danger">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            <?= abs($daysUntil) ?> Tage überfällig
                                        </small>
                                    <?php elseif ($daysUntil <= 7): ?>
                                        <small class="text-warning">
                                            <i class="bi bi-clock me-1"></i>
                                            In <?= $daysUntil ?> Tagen
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">
                                            In <?= $daysUntil ?> Tagen
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($schedule['is_active']): ?>
                                        <span class="badge bg-success">Aktiv</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inaktiv</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($schedule['auto_generate_work_orders']): ?>
                                        <small class="d-block text-muted">
                                            <i class="bi bi-gear me-1"></i>Auto-WO
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url("preventive-maintenance/{$schedule['id']}") ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Details anzeigen">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url("preventive-maintenance/{$schedule['id']}/edit") ?>" 
                                           class="btn btn-sm btn-outline-secondary" title="Bearbeiten">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($isOverdue || $daysUntil <= 0): ?>
                                            <button class="btn btn-sm btn-outline-success" 
                                                    onclick="markCompleted(<?= $schedule['id'] ?>)" 
                                                    title="Als abgeschlossen markieren">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?= $schedule['id'] ?>)" 
                                                title="Löschen">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x display-1 text-muted"></i>
                <h4 class="mt-3">Keine Instandhaltungspläne gefunden</h4>
                <p class="text-muted">
                    <?php if ($search || $status): ?>
                        Keine Instandhaltungspläne entsprechen Ihren Filterkriterien.
                    <?php else: ?>
                        Erstellen Sie Ihren ersten Instandhaltungsplan, um mit der Instandhaltung zu beginnen.
                    <?php endif; ?>
                </p>
                <a href="<?= base_url('preventive-maintenance/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Ersten Instandhaltungsplan erstellen
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function generateWorkOrders() {
    if (confirm('Möchten Sie Arbeitsaufträge für alle fälligen Instandhaltungen generieren?')) {
        fetch('<?= base_url('preventive-maintenance/generate-work-orders') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Fehler: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
            alert('Ein Fehler ist aufgetreten.');
        });
    }
}

function markCompleted(scheduleId) {
    const completedDate = prompt('Instandhaltung abgeschlossen am (YYYY-MM-DD HH:MM):', 
                                new Date().toISOString().slice(0, 16).replace('T', ' '));
    
    if (completedDate) {
        fetch(`<?= base_url('preventive-maintenance') ?>/${scheduleId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                completed_date: completedDate
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Fehler: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
            alert('Ein Fehler ist aufgetreten.');
        });
    }
}

function confirmDelete(scheduleId) {
    if (confirm('Sind Sie sicher, dass Sie diesen Instandhaltungsplan löschen möchten?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= base_url('preventive-maintenance') ?>/${scheduleId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>