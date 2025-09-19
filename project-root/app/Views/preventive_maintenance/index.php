<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0"><?= $page_title ?></h2>
        <p class="text-muted">Verwaltung von Instandhaltungsplänen</p>
    </div>
    <div>
        <button class="btn btn-success me-2" onclick="showWorkOrderSelectionModal()">
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

<!-- Work Order Selection Modal -->
<div class="modal fade" id="workOrderSelectionModal" tabindex="-1" aria-labelledby="workOrderSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workOrderSelectionModalLabel">
                    <i class="bi bi-gear-wide-connected me-2"></i>
                    Wiederkehrende Instandhaltungspläne aus erledigten Arbeitsaufträgen erstellen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Wählen Sie Arbeitsaufträge vom Typ Instandhaltung oder Inspektion aus und definieren Sie Intervalle für die automatische Generierung zukünftiger Instandhaltungsaufgaben.
                </div>

                <div id="workOrdersList" class="table-responsive">
                    <div class="d-flex justify-content-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Wird geladen...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-primary" id="createSchedulesBtn" onclick="createSchedulesFromSelection()" disabled>
                    <i class="bi bi-plus-circle me-1"></i>
                    Instandhaltungspläne erstellen
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let completedWorkOrders = [];
let selectedWorkOrders = [];

function showWorkOrderSelectionModal() {
    const modal = new bootstrap.Modal(document.getElementById('workOrderSelectionModal'));
    modal.show();

    loadCompletedWorkOrders();
}

function loadCompletedWorkOrders() {
    fetch('<?= base_url('api/preventive-maintenance/completed-work-orders') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                completedWorkOrders = data.data;
                renderWorkOrdersList();
            } else {
                document.getElementById('workOrdersList').innerHTML =
                    '<div class="alert alert-warning">Keine Instandhaltungs- oder Inspektionsarbeitsaufträge gefunden.</div>';
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
            document.getElementById('workOrdersList').innerHTML =
                '<div class="alert alert-danger">Fehler beim Laden der Arbeitsaufträge.</div>';
        });
}

function renderWorkOrdersList() {
    if (completedWorkOrders.length === 0) {
        document.getElementById('workOrdersList').innerHTML =
            '<div class="alert alert-warning">Keine Instandhaltungs- oder Inspektionsarbeitsaufträge gefunden.</div>';
        return;
    }

    let html = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6>Instandhaltung/Inspektion Arbeitsaufträge (${completedWorkOrders.length} gefunden)</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllWorkOrders()">Alle auswählen</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllWorkOrders()">Alle abwählen</button>
            </div>
        </div>
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th width="40">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                    </th>
                    <th>Arbeitsauftrag</th>
                    <th>Anlage</th>
                    <th>Beschreibung</th>
                    <th>Priorität</th>
                    <th>Erstellt</th>
                    <th>Intervall</th>
                </tr>
            </thead>
            <tbody>`;

    completedWorkOrders.forEach(workOrder => {
        html += `
            <tr class="workorder-row" data-id="${workOrder.id}">
                <td>
                    <input type="checkbox" class="workorder-checkbox" value="${workOrder.id}"
                           onchange="toggleWorkOrderSelection(${workOrder.id})">
                </td>
                <td>
                    <div class="fw-bold">${workOrder.title || 'Ohne Titel'}</div>
                    <small class="text-muted">${workOrder.work_order_number || 'N/A'}</small>
                </td>
                <td>
                    <div>${workOrder.asset_name || 'Keine Anlage'}</div>
                    <small class="text-muted">${workOrder.asset_number || ''}</small>
                </td>
                <td>
                    <div class="text-truncate" style="max-width: 200px;" title="${workOrder.description || ''}">
                        ${workOrder.description || 'Keine Beschreibung'}
                    </div>
                </td>
                <td>
                    <span class="badge bg-${getPriorityColor(workOrder.priority)}">${workOrder.priority || 'medium'}</span>
                </td>
                <td>
                    <small>${workOrder.created_at ? new Date(workOrder.created_at).toLocaleDateString('de-DE') : 'N/A'}</small>
                </td>
                <td>
                    <div class="interval-controls" id="interval-${workOrder.id}" style="display: none;">
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control" min="1" value="1"
                                   id="interval-value-${workOrder.id}" style="max-width: 60px;">
                            <select class="form-select" id="interval-type-${workOrder.id}" style="max-width: 120px;">
                                <option value="daily">Tage</option>
                                <option value="weekly">Wochen</option>
                                <option value="monthly" selected>Monate</option>
                                <option value="quarterly">Quartale</option>
                                <option value="annually">Jahre</option>
                            </select>
                        </div>
                    </div>
                </td>
            </tr>`;
    });

    html += '</tbody></table>';

    document.getElementById('workOrdersList').innerHTML = html;
}

function toggleWorkOrderSelection(workOrderId) {
    const checkbox = document.querySelector(`input[value="${workOrderId}"]`);
    const intervalControls = document.getElementById(`interval-${workOrderId}`);

    if (checkbox.checked) {
        selectedWorkOrders.push(workOrderId);
        intervalControls.style.display = 'block';
    } else {
        selectedWorkOrders = selectedWorkOrders.filter(id => id !== workOrderId);
        intervalControls.style.display = 'none';
    }

    updateCreateButton();
}

function selectAllWorkOrders() {
    document.querySelectorAll('.workorder-checkbox').forEach(checkbox => {
        checkbox.checked = true;
        toggleWorkOrderSelection(parseInt(checkbox.value));
    });
    document.getElementById('selectAllCheckbox').checked = true;
}

function deselectAllWorkOrders() {
    document.querySelectorAll('.workorder-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        toggleWorkOrderSelection(parseInt(checkbox.value));
    });
    selectedWorkOrders = [];
    document.getElementById('selectAllCheckbox').checked = false;
    updateCreateButton();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    if (selectAllCheckbox.checked) {
        selectAllWorkOrders();
    } else {
        deselectAllWorkOrders();
    }
}

function updateCreateButton() {
    const createBtn = document.getElementById('createSchedulesBtn');
    createBtn.disabled = selectedWorkOrders.length === 0;

    if (selectedWorkOrders.length > 0) {
        createBtn.innerHTML = `<i class="bi bi-plus-circle me-1"></i>Instandhaltungspläne erstellen (${selectedWorkOrders.length})`;
    } else {
        createBtn.innerHTML = `<i class="bi bi-plus-circle me-1"></i>Instandhaltungspläne erstellen`;
    }
}

function createSchedulesFromSelection() {
    if (selectedWorkOrders.length === 0) {
        alert('Bitte wählen Sie mindestens einen Arbeitsauftrag aus.');
        return;
    }

    // Collect selected work orders with their interval settings
    const selectedData = selectedWorkOrders.map(workOrderId => {
        const intervalValue = document.getElementById(`interval-value-${workOrderId}`).value;
        const intervalType = document.getElementById(`interval-type-${workOrderId}`).value;

        return {
            work_order_id: workOrderId,
            interval_value: parseInt(intervalValue),
            interval_type: intervalType
        };
    });

    const createBtn = document.getElementById('createSchedulesBtn');
    createBtn.disabled = true;
    createBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Erstelle Pläne...';

    fetch('<?= base_url('api/preventive-maintenance/create-schedules-from-work-orders') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            selected_orders: selectedData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('workOrderSelectionModal')).hide();
            location.reload();
        } else {
            alert('Fehler: ' + data.message + (data.errors ? '\n\nDetails:\n' + data.errors.join('\n') : ''));
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
        alert('Ein Fehler ist aufgetreten.');
    })
    .finally(() => {
        createBtn.disabled = false;
        updateCreateButton();
    });
}

function getPriorityColor(priority) {
    switch (priority) {
        case 'critical': return 'danger';
        case 'high': return 'warning';
        case 'medium': return 'info';
        case 'low': return 'secondary';
        default: return 'info';
    }
}

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