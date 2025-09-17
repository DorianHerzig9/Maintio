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
                            <option value="open" <?= $status_filter === 'open' ? 'selected' : '' ?>>Offen</option>
                            <option value="in_progress" <?= $status_filter === 'in_progress' ? 'selected' : '' ?>>In Bearbeitung</option>
                            <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Abgeschlossen</option>
                            <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Abgebrochen</option>
                            <option value="on_hold" <?= $status_filter === 'on_hold' ? 'selected' : '' ?>>Wartend</option>
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
                        <input type="text" class="form-control" id="search" placeholder="Auftragsnummer, Titel, Beschreibung...">
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
                <i class="bi bi-clipboard-check me-2"></i>
                Arbeitsaufträge (<?= count($work_orders) ?>)
            </h5>
            <a href="<?= base_url('work-orders/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Neuer Arbeitsauftrag
            </a>
        </div>
    </div>
</div>

<!-- Work Orders Tabelle -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($work_orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Auftragsnummer</th>
                                    <th>Titel</th>
                                    <th>Typ</th>
                                    <th>Status</th>
                                    <th>Priorität</th>
                                    <th>Anlage</th>
                                    <th>Zugewiesen</th>
                                    <th>Erstellt</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($work_orders as $order): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('work-orders/' . $order['id']) ?>" class="text-decoration-none">
                                                <strong><?= esc($order['work_order_number']) ?></strong>
                                            </a>
                                        </td>
                                        <td>
                                            <div><?= esc($order['title']) ?></div>
                                            <?php if (!empty($order['description'])): ?>
                                                <small class="text-muted">
                                                    <?= esc(substr($order['description'], 0, 50)) ?>
                                                    <?= strlen($order['description']) > 50 ? '...' : '' ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= getTypeColor($order['type']) ?>">
                                                <?= getTypeText($order['type']) ?>
                                            </span>
                                        </td>
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
                                        <td>
                                            <?php if (!empty($order['asset_name'])): ?>
                                                <small><?= esc($order['asset_name']) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($order['assigned_username'])): ?>
                                                <small><?= esc($order['assigned_username']) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Nicht zugewiesen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= date('d.m.Y', strtotime($order['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= base_url('work-orders/' . $order['id']) ?>" 
                                                   class="btn btn-outline-primary" title="Anzeigen">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('work-orders/' . $order['id'] . '/edit') ?>" 
                                                   class="btn btn-outline-secondary" title="Bearbeiten">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-outline-danger" 
                                                        onclick="deleteWorkOrder(<?= $order['id'] ?>)" 
                                                        title="Löschen">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <?php if ($order['status'] === 'open'): ?>
                                                    <button class="btn btn-outline-success" 
                                                            onclick="updateStatus(<?= $order['id'] ?>, 'in_progress')" 
                                                            title="Starten">
                                                        <i class="bi bi-play"></i>
                                                    </button>
                                                <?php elseif ($order['status'] === 'in_progress'): ?>
                                                    <button class="btn btn-outline-success" 
                                                            onclick="updateStatus(<?= $order['id'] ?>, 'completed')" 
                                                            title="Abschließen">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h5 class="mt-3">Keine Arbeitsaufträge gefunden</h5>
                        <p class="text-muted">Erstellen Sie Ihren ersten Arbeitsauftrag oder passen Sie die Filter an.</p>
                        <a href="<?= base_url('work-orders/create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            Arbeitsauftrag erstellen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function updateStatus(workOrderId, newStatus) {
    if (confirm('Möchten Sie den Status wirklich ändern?')) {
        fetch(`<?= base_url('work-orders') ?>/${workOrderId}/status`, {
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

function deleteWorkOrder(workOrderId) {
    if (confirm('Möchten Sie diesen Arbeitsauftrag wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.')) {
        fetch(`<?= base_url('work-orders') ?>/${workOrderId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Fehler: ' + (data.message || 'Arbeitsauftrag konnte nicht gelöscht werden'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ein Fehler ist aufgetreten: ' + error.message);
        });
    }
}

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
// Helper functions für die View
function getTypeColor($type) {
    switch ($type) {
        case 'instandhaltung': return 'info';
        case 'instandsetzung': return 'warning';
        case 'notfall': return 'danger';
        case 'inspektion': return 'secondary';
        default: return 'secondary';
    }
}

function getTypeText($type) {
    switch ($type) {
        case 'instandhaltung': return 'Instandhaltung';
        case 'instandsetzung': return 'Instandsetzung';
        case 'notfall': return 'Notfall';
        case 'inspektion': return 'Inspektion';
        default: return ucfirst($type);
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
