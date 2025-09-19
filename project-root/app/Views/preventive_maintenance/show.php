<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0"><?= $page_title ?></h2>
        <p class="text-muted">Details des Instandhaltungsplans</p>
    </div>
    <div>
        <a href="<?= base_url("preventive-maintenance/{$schedule['id']}/edit") ?>" class="btn btn-primary me-2">
            <i class="bi bi-pencil me-1"></i>
            Bearbeiten
        </a>
        <a href="<?= base_url('preventive-maintenance') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Zurück zur Übersicht
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Grundinformationen
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3"><strong>Name:</strong></div>
                    <div class="col-sm-9"><?= esc($schedule['schedule_name']) ?></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3"><strong>Anlage:</strong></div>
                    <div class="col-sm-9">
                        <a href="<?= base_url("assets/{$schedule['asset_id']}") ?>" class="text-decoration-none">
                            <?= esc($schedule['asset_name']) ?> (<?= esc($schedule['asset_number']) ?>)
                        </a>
                    </div>
                </div>
                <?php if (!empty($schedule['description'])): ?>
                <hr>
                <div class="row">
                    <div class="col-sm-3"><strong>Beschreibung:</strong></div>
                    <div class="col-sm-9"><?= nl2br(esc($schedule['description'])) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($schedule['task_details'])): ?>
                <hr>
                <div class="row">
                    <div class="col-sm-3"><strong>Arbeitsanweisungen:</strong></div>
                    <div class="col-sm-9"><?= nl2br(esc($schedule['task_details'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Scheduling Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-event me-2"></i>
                    Zeitplanung
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3"><strong>Intervall:</strong></div>
                    <div class="col-sm-9">
                        <span class="badge bg-info">
                            <?= $schedule['interval_value'] ?>
                            <?= App\Models\PreventiveMaintenanceModel::getIntervalTypeText($schedule['interval_type']) ?>
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3"><strong>Priorität:</strong></div>
                    <div class="col-sm-9">
                        <span class="badge bg-<?= App\Models\PreventiveMaintenanceModel::getPriorityColor($schedule['priority']) ?>">
                            <?= ucfirst($schedule['priority']) ?>
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3"><strong>Nächste Instandhaltung:</strong></div>
                    <div class="col-sm-9">
                        <?php
                        $nextDue = new DateTime($schedule['next_due']);
                        $today = new DateTime();
                        $isOverdue = $nextDue < $today;
                        $daysUntil = $today->diff($nextDue)->format('%R%a');
                        ?>
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
                    </div>
                </div>
                <?php if (!empty($schedule['last_completed'])): ?>
                <hr>
                <div class="row">
                    <div class="col-sm-3"><strong>Letzte Durchführung:</strong></div>
                    <div class="col-sm-9">
                        <?= (new DateTime($schedule['last_completed']))->format('d.m.Y H:i') ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($schedule['estimated_duration'])): ?>
                <hr>
                <div class="row">
                    <div class="col-sm-3"><strong>Geschätzte Dauer:</strong></div>
                    <div class="col-sm-9"><?= $schedule['estimated_duration'] ?> Minuten</div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Additional Details -->
        <?php if (!empty($schedule['required_tools']) || !empty($schedule['required_parts']) || !empty($schedule['safety_notes'])): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tools me-2"></i>
                    Zusätzliche Informationen
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($schedule['required_tools'])): ?>
                <div class="row">
                    <div class="col-sm-3"><strong>Benötigte Werkzeuge:</strong></div>
                    <div class="col-sm-9"><?= nl2br(esc($schedule['required_tools'])) ?></div>
                </div>
                <hr>
                <?php endif; ?>
                <?php if (!empty($schedule['required_parts'])): ?>
                <div class="row">
                    <div class="col-sm-3"><strong>Benötigte Ersatzteile:</strong></div>
                    <div class="col-sm-9"><?= nl2br(esc($schedule['required_parts'])) ?></div>
                </div>
                <hr>
                <?php endif; ?>
                <?php if (!empty($schedule['safety_notes'])): ?>
                <div class="row">
                    <div class="col-sm-3"><strong>Sicherheitshinweise:</strong></div>
                    <div class="col-sm-9"><?= nl2br(esc($schedule['safety_notes'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status and Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Status & Aktionen
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <div>
                        <?php if ($schedule['is_active']): ?>
                            <span class="badge bg-success">Aktiv</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inaktiv</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($schedule['category'])): ?>
                <div class="mb-3">
                    <label class="form-label">Kategorie</label>
                    <div>
                        <span class="badge bg-primary"><?= esc($schedule['category']) ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($schedule['assigned_user'])): ?>
                <div class="mb-3">
                    <label class="form-label">Standard-Zuordnung</label>
                    <div><?= esc($schedule['assigned_user']) ?></div>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Arbeitsauftrag-Einstellungen</label>
                    <div>
                        <?php if ($schedule['auto_generate_work_orders']): ?>
                            <small class="text-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Automatische Generierung aktiviert
                            </small>
                            <div class="small text-muted">
                                Vorlaufzeit: <?= $schedule['lead_time_days'] ?> Tage
                            </div>
                        <?php else: ?>
                            <small class="text-muted">
                                <i class="bi bi-x-circle me-1"></i>
                                Automatische Generierung deaktiviert
                            </small>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <?php if ($isOverdue || $daysUntil <= 0): ?>
                        <button class="btn btn-success" onclick="markCompleted(<?= $schedule['id'] ?>)">
                            <i class="bi bi-check-circle me-1"></i>
                            Als abgeschlossen markieren
                        </button>
                    <?php endif; ?>

                    <a href="<?= base_url("preventive-maintenance/{$schedule['id']}/edit") ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>
                        Bearbeiten
                    </a>

                    <button class="btn btn-outline-danger" onclick="confirmDelete(<?= $schedule['id'] ?>)">
                        <i class="bi bi-trash me-1"></i>
                        Löschen
                    </button>
                </div>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Timestamps
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Erstellt:</small><br>
                    <?= (new DateTime($schedule['created_at']))->format('d.m.Y H:i:s') ?>
                </div>
                <div>
                    <small class="text-muted">Letzte Änderung:</small><br>
                    <?= (new DateTime($schedule['updated_at']))->format('d.m.Y H:i:s') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
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