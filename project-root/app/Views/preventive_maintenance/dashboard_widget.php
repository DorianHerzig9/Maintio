<!-- Preventive Maintenance Widget -->
<div class="col-xl-6 col-lg-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bi bi-calendar-check me-2"></i>
                Instandhaltung
            </h5>
            <a href="<?= base_url('preventive-maintenance') ?>" class="btn btn-sm btn-outline-primary">
                Alle anzeigen
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($overdue_maintenance)): ?>
                <!-- Overdue Maintenance -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        <h6 class="text-danger mb-0">Überfällige Instandhaltungen (<?= count($overdue_maintenance) ?>)</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($overdue_maintenance, 0, 3) as $maintenance): ?>
                            <?php 
                            $nextDue = new DateTime($maintenance['next_due']);
                            $today = new DateTime();
                            $daysOverdue = $today->diff($nextDue)->format('%a');
                            ?>
                            <div class="list-group-item p-2 border-start border-danger border-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold"><?= esc($maintenance['schedule_name']) ?></div>
                                        <small class="text-muted">
                                            <?= esc($maintenance['asset_name']) ?> (<?= esc($maintenance['asset_number']) ?>)
                                        </small>
                                        <div class="mt-1">
                                            <small class="text-danger">
                                                <i class="bi bi-clock me-1"></i>
                                                <?= $daysOverdue ?> Tage überfällig
                                            </small>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <span class="badge bg-<?= App\Models\PreventiveMaintenanceModel::getPriorityColor($maintenance['priority']) ?> rounded-pill">
                                            <?= ucfirst($maintenance['priority']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($overdue_maintenance) > 3): ?>
                            <div class="list-group-item p-2 text-center">
                                <small class="text-muted">
                                    und <?= count($overdue_maintenance) - 3 ?> weitere überfällige Instandhaltungen...
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($upcoming_maintenance)): ?>
                <!-- Upcoming Maintenance -->
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-calendar-event text-warning me-2"></i>
                        <h6 class="text-warning mb-0">Anstehende Instandhaltungen (<?= count($upcoming_maintenance) ?>)</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($upcoming_maintenance, 0, 5) as $maintenance): ?>
                            <?php 
                            $nextDue = new DateTime($maintenance['next_due']);
                            $today = new DateTime();
                            $daysUntil = $today->diff($nextDue)->format('%a');
                            ?>
                            <div class="list-group-item p-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold"><?= esc($maintenance['schedule_name']) ?></div>
                                        <small class="text-muted">
                                            <?= esc($maintenance['asset_name']) ?> (<?= esc($maintenance['asset_number']) ?>)
                                        </small>
                                        <div class="mt-1">
                                            <small class="text-primary">
                                                <i class="bi bi-calendar me-1"></i>
                                                <?= $nextDue->format('d.m.Y') ?> (in <?= $daysUntil ?> Tagen)
                                            </small>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <span class="badge bg-<?= App\Models\PreventiveMaintenanceModel::getPriorityColor($maintenance['priority']) ?> rounded-pill">
                                            <?= ucfirst($maintenance['priority']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($upcoming_maintenance) > 5): ?>
                            <div class="list-group-item p-2 text-center">
                                <small class="text-muted">
                                    und <?= count($upcoming_maintenance) - 5 ?> weitere anstehende Instandhaltungen...
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($overdue_maintenance) && empty($upcoming_maintenance)): ?>
                <!-- No Maintenance -->
                <div class="text-center py-4">
                    <i class="bi bi-check-circle display-6 text-success"></i>
                    <h6 class="mt-2 mb-1">Alle Instandhaltungen sind auf dem neuesten Stand</h6>
                    <small class="text-muted">Keine überfälligen oder anstehenden Instandhaltungen in den nächsten 14 Tagen</small>
                </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="mt-3 pt-3 border-top">
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn btn-success btn-sm w-100" onclick="generatePreventiveWorkOrders()">
                            <i class="bi bi-gear-wide-connected me-1"></i>
                            WO generieren
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('preventive-maintenance/create') ?>" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus-circle me-1"></i>
                            Neuer Plan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generatePreventiveWorkOrders() {
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
</script>