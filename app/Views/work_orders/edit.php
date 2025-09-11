<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Arbeitsauftrag bearbeiten
                </h5>
                <small class="text-muted"><?= esc($work_order['work_order_number']) ?></small>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <h6>Bitte korrigieren Sie folgende Fehler:</h6>
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('work-orders/' . $work_order['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label">Titel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= old('title', $work_order['title']) ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="open" <?= old('status', $work_order['status']) === 'open' ? 'selected' : '' ?>>Offen</option>
                                <option value="in_progress" <?= old('status', $work_order['status']) === 'in_progress' ? 'selected' : '' ?>>In Bearbeitung</option>
                                <option value="completed" <?= old('status', $work_order['status']) === 'completed' ? 'selected' : '' ?>>Abgeschlossen</option>
                                <option value="cancelled" <?= old('status', $work_order['status']) === 'cancelled' ? 'selected' : '' ?>>Abgebrochen</option>
                                <option value="on_hold" <?= old('status', $work_order['status']) === 'on_hold' ? 'selected' : '' ?>>Wartend</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="priority" class="form-label">Priorität <span class="text-danger">*</span></label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="low" <?= old('priority', $work_order['priority']) === 'low' ? 'selected' : '' ?>>Niedrig</option>
                                <option value="medium" <?= old('priority', $work_order['priority']) === 'medium' ? 'selected' : '' ?>>Mittel</option>
                                <option value="high" <?= old('priority', $work_order['priority']) === 'high' ? 'selected' : '' ?>>Hoch</option>
                                <option value="critical" <?= old('priority', $work_order['priority']) === 'critical' ? 'selected' : '' ?>>Kritisch</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Typ <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="preventive" <?= old('type', $work_order['type']) === 'preventive' ? 'selected' : '' ?>>Vorbeugend</option>
                                <option value="corrective" <?= old('type', $work_order['type']) === 'corrective' ? 'selected' : '' ?>>Korrektiv</option>
                                <option value="emergency" <?= old('type', $work_order['type']) === 'emergency' ? 'selected' : '' ?>>Notfall</option>
                                <option value="inspection" <?= old('type', $work_order['type']) === 'inspection' ? 'selected' : '' ?>>Inspektion</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="asset_id" class="form-label">Anlage</label>
                            <select class="form-select" id="asset_id" name="asset_id">
                                <option value="">Keine Anlage zugeordnet</option>
                                <?php foreach ($assets as $asset): ?>
                                    <option value="<?= $asset['id'] ?>" <?= old('asset_id', $work_order['asset_id']) == $asset['id'] ? 'selected' : '' ?>>
                                        <?= esc($asset['name']) ?> (<?= esc($asset['asset_number']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="assigned_user_id" class="form-label">Zugewiesen an</label>
                            <select class="form-select" id="assigned_user_id" name="assigned_user_id">
                                <option value="">Noch nicht zugewiesen</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= old('assigned_user_id', $work_order['assigned_user_id']) == $user['id'] ? 'selected' : '' ?>>
                                        <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> (<?= esc($user['username']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_date" class="form-label">Geplantes Datum</label>
                            <input type="datetime-local" class="form-control" id="scheduled_date" name="scheduled_date" 
                                   value="<?= old('scheduled_date', $work_order['scheduled_date'] ? date('Y-m-d\TH:i', strtotime($work_order['scheduled_date'])) : '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="estimated_duration" class="form-label">Geschätzte Dauer (Minuten)</label>
                            <input type="number" class="form-control" id="estimated_duration" name="estimated_duration" 
                                   value="<?= old('estimated_duration', $work_order['estimated_duration']) ?>" min="1">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="actual_duration" class="form-label">Tatsächliche Dauer (Minuten)</label>
                            <input type="number" class="form-control" id="actual_duration" name="actual_duration" 
                                   value="<?= old('actual_duration', $work_order['actual_duration']) ?>" min="1">
                            <div class="form-text">Nur für abgeschlossene Aufträge</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= old('description', $work_order['description']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">Notizen</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $work_order['notes']) ?></textarea>
                        <div class="form-text">Zusätzliche Notizen zur Bearbeitung</div>
                    </div>

                    <!-- Zeitstempel (read-only) -->
                    <?php if ($work_order['started_at'] || $work_order['completed_at']): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6>Zeitstempel</h6>
                                <div class="row">
                                    <?php if ($work_order['started_at']): ?>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Gestartet am:</label>
                                            <input type="text" class="form-control-plaintext" 
                                                   value="<?= date('d.m.Y H:i', strtotime($work_order['started_at'])) ?>" readonly>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($work_order['completed_at']): ?>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Abgeschlossen am:</label>
                                            <input type="text" class="form-control-plaintext" 
                                                   value="<?= date('d.m.Y H:i', strtotime($work_order['completed_at'])) ?>" readonly>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="<?= base_url('work-orders/' . $work_order['id']) ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Zurück
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Änderungen speichern
                            </button>
                            <a href="<?= base_url('work-orders') ?>" class="btn btn-outline-secondary ms-2">
                                Abbrechen
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Status-abhängige Logik
document.getElementById('status').addEventListener('change', function() {
    const status = this.value;
    const actualDurationField = document.getElementById('actual_duration');
    const actualDurationLabel = actualDurationField.previousElementSibling;
    
    // Tatsächliche Dauer nur bei abgeschlossenen Aufträgen relevant
    if (status === 'completed') {
        actualDurationField.removeAttribute('disabled');
        actualDurationLabel.innerHTML = 'Tatsächliche Dauer (Minuten) <span class="text-danger">*</span>';
        actualDurationField.setAttribute('required', 'required');
    } else {
        actualDurationField.removeAttribute('required');
        actualDurationLabel.innerHTML = 'Tatsächliche Dauer (Minuten)';
    }
});

// Auto-Berechnung der tatsächlichen Dauer basierend auf Start- und Endzeit
function calculateActualDuration() {
    const startedAt = '<?= $work_order['started_at'] ?>';
    const completedAt = '<?= $work_order['completed_at'] ?>';
    
    if (startedAt && completedAt) {
        const start = new Date(startedAt);
        const end = new Date(completedAt);
        const diffMinutes = Math.round((end - start) / (1000 * 60));
        
        const actualDurationField = document.getElementById('actual_duration');
        if (!actualDurationField.value && diffMinutes > 0) {
            actualDurationField.value = diffMinutes;
        }
    }
}

// Beim Laden der Seite ausführen
document.addEventListener('DOMContentLoaded', function() {
    // Status-change Event beim Laden triggern
    document.getElementById('status').dispatchEvent(new Event('change'));
    
    // Dauer berechnen
    calculateActualDuration();
});

// Validierung vor dem Absenden
document.querySelector('form').addEventListener('submit', function(e) {
    const status = document.getElementById('status').value;
    const actualDuration = document.getElementById('actual_duration').value;
    
    if (status === 'completed' && !actualDuration) {
        e.preventDefault();
        alert('Bitte geben Sie die tatsächliche Dauer für abgeschlossene Aufträge an.');
        document.getElementById('actual_duration').focus();
        return false;
    }
});
</script>
<?= $this->endSection() ?>
