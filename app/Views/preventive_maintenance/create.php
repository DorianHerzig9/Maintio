<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0"><?= $page_title ?></h2>
        <p class="text-muted">Neuen Instandhaltungsplan erstellen</p>
    </div>
    <div>
        <a href="<?= base_url('preventive-maintenance') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Zurück zur Übersicht
        </a>
    </div>
</div>

<?php if (session('validation')): ?>
    <div class="alert alert-danger">
        <h6>Bitte korrigieren Sie folgende Fehler:</h6>
        <ul class="mb-0">
            <?php foreach (session('validation')->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= base_url('preventive-maintenance') ?>">
    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Grundinformationen
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="schedule_name" class="form-label">Name des Instandhaltungsplans <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="schedule_name" name="schedule_name" 
                                       value="<?= esc(old('schedule_name', $schedule['schedule_name'] ?? '')) ?>" required>
                                <div class="form-text">Z.B. "Monatliche Filterreinigung"</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="asset_id" class="form-label">Anlage <span class="text-danger">*</span></label>
                                <select class="form-select" id="asset_id" name="asset_id" required>
                                    <option value="">Anlage auswählen...</option>
                                    <?php foreach ($assets as $asset): ?>
                                        <option value="<?= $asset['id'] ?>" 
                                                <?= old('asset_id', $schedule['asset_id'] ?? '') == $asset['id'] ? 'selected' : '' ?>>
                                            <?= esc($asset['name']) ?> (<?= esc($asset['asset_number']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= esc(old('description', $schedule['description'] ?? '')) ?></textarea>
                        <div class="form-text">Kurze Beschreibung des Instandhaltungsplans</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="task_details" class="form-label">Detaillierte Arbeitsanweisungen</label>
                        <textarea class="form-control" id="task_details" name="task_details" rows="5"><?= esc(old('task_details', $schedule['task_details'] ?? '')) ?></textarea>
                                                <div class="form-text">Schritt-für-Schritt Anweisungen für die Instandhaltung</div>
                    </div>
                </div>
            </div>
            
            <!-- Scheduling -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-event me-2"></i>
                        Zeitplanung
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="interval_type" class="form-label">Intervall-Typ <span class="text-danger">*</span></label>
                                <select class="form-select" id="interval_type" name="interval_type" required>
                                    <option value="">Typ auswählen...</option>
                                    <option value="daily" <?= old('interval_type', $schedule['interval_type'] ?? '') == 'daily' ? 'selected' : '' ?>>Täglich</option>
                                    <option value="weekly" <?= old('interval_type', $schedule['interval_type'] ?? '') == 'weekly' ? 'selected' : '' ?>>Wöchentlich</option>
                                    <option value="monthly" <?= old('interval_type', $schedule['interval_type'] ?? '') == 'monthly' ? 'selected' : '' ?>>Monatlich</option>
                                    <option value="quarterly" <?= old('interval_type', $schedule['interval_type'] ?? '') == 'quarterly' ? 'selected' : '' ?>>Quartalsweise</option>
                                    <option value="annually" <?= old('interval_type', $schedule['interval_type'] ?? '') == 'annually' ? 'selected' : '' ?>>Jährlich</option>
                                    <option value="hours" <?= old('interval_type', $schedule['interval_type'] ?? '') == 'hours' ? 'selected' : '' ?>>Betriebsstunden</option>
                                    <option value="cycles" <?= old('interval_type', $schedule['interval_type'] ?? '') == 'cycles' ? 'selected' : '' ?>>Zyklen</option>
                                    <option value="kilometers" <?= old('interval_type', $schedule['interval_type'] ?? '') == 'kilometers' ? 'selected' : '' ?>>Kilometer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="interval_value" class="form-label">Intervall-Wert <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="interval_value" name="interval_value" 
                                       value="<?= esc(old('interval_value', $schedule['interval_value'] ?? '')) ?>" 
                                       min="1" required>
                                <div class="form-text">Z.B. 30 für "alle 30 Tage"</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priorität <span class="text-danger">*</span></label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="">Priorität auswählen...</option>
                                    <option value="low" <?= old('priority', $schedule['priority'] ?? '') == 'low' ? 'selected' : '' ?>>Niedrig</option>
                                    <option value="medium" <?= old('priority', $schedule['priority'] ?? 'medium') == 'medium' ? 'selected' : '' ?>>Mittel</option>
                                    <option value="high" <?= old('priority', $schedule['priority'] ?? '') == 'high' ? 'selected' : '' ?>>Hoch</option>
                                    <option value="critical" <?= old('priority', $schedule['priority'] ?? '') == 'critical' ? 'selected' : '' ?>>Kritisch</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estimated_duration" class="form-label">Geschätzte Dauer (Minuten)</label>
                                <input type="number" class="form-control" id="estimated_duration" name="estimated_duration" 
                                       value="<?= esc(old('estimated_duration', $schedule['estimated_duration'] ?? '')) ?>" 
                                       min="1">
                                <div class="form-text">Für Planung und Zeiterfassung</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-tools me-2"></i>
                        Zusätzliche Informationen
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategorie</label>
                        <input type="text" class="form-control" id="category" name="category" 
                               value="<?= esc(old('category', $schedule['category'] ?? '')) ?>"
                               placeholder="Z.B. Elektrik, Mechanik, Sicherheit">
                        <div class="form-text">Hilft bei der Organisation und Filterung</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="required_tools" class="form-label">Benötigte Werkzeuge</label>
                        <textarea class="form-control" id="required_tools" name="required_tools" rows="3"><?= esc(old('required_tools', $schedule['required_tools'] ?? '')) ?></textarea>
                        <div class="form-text">Liste der benötigten Werkzeuge und Ausrüstung</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="required_parts" class="form-label">Benötigte Ersatzteile</label>
                        <textarea class="form-control" id="required_parts" name="required_parts" rows="3"><?= esc(old('required_parts', $schedule['required_parts'] ?? '')) ?></textarea>
                        <div class="form-text">Typischerweise benötigte Ersatzteile und Materialien</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="safety_notes" class="form-label">Sicherheitshinweise</label>
                        <textarea class="form-control" id="safety_notes" name="safety_notes" rows="3"><?= esc(old('safety_notes', $schedule['safety_notes'] ?? '')) ?></textarea>
                        <div class="form-text">Wichtige Sicherheitsvorkehrungen und Warnungen</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Settings Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Einstellungen
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="assigned_user_id" class="form-label">Standard-Zuordnung</label>
                        <select class="form-select" id="assigned_user_id" name="assigned_user_id">
                            <option value="">Kein Standard-Benutzer</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" 
                                        <?= old('assigned_user_id', $schedule['assigned_user_id'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                                    <?= esc($user['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Arbeitsaufträge werden diesem Benutzer zugewiesen</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lead_time_days" class="form-label">Vorlaufzeit (Tage)</label>
                        <input type="number" class="form-control" id="lead_time_days" name="lead_time_days" 
                               value="<?= esc(old('lead_time_days', $schedule['lead_time_days'] ?? '7')) ?>" 
                               min="0" max="365">
                        <div class="form-text">Arbeitsauftrag wird X Tage vor Fälligkeit erstellt</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="auto_generate_work_orders" 
                                   name="auto_generate_work_orders" value="1"
                                   <?= old('auto_generate_work_orders', $schedule['auto_generate_work_orders'] ?? '1') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="auto_generate_work_orders">
                                Arbeitsaufträge automatisch generieren
                            </label>
                        </div>
                        <div class="form-text">System erstellt automatisch Arbeitsaufträge bei Fälligkeit</div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="card mt-3">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                        <i class="bi bi-check-circle me-1"></i>
                        Instandhaltungsplan erstellen
                    </button>
                    <a href="<?= base_url('preventive-maintenance') ?>" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i>
                        Abbrechen
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Update interval value label based on interval type
document.getElementById('interval_type').addEventListener('change', function() {
    const intervalValue = document.getElementById('interval_value');
    const helpText = intervalValue.parentElement.querySelector('.form-text');
    
    switch(this.value) {
        case 'daily':
            helpText.textContent = 'Z.B. 1 für "täglich", 7 für "alle 7 Tage"';
            break;
        case 'weekly':
            helpText.textContent = 'Z.B. 2 für "alle 2 Wochen"';
            break;
        case 'monthly':
            helpText.textContent = 'Z.B. 3 für "alle 3 Monate"';
            break;
        case 'quarterly':
            helpText.textContent = 'Z.B. 1 für "jedes Quartal", 2 für "alle 6 Monate"';
            break;
        case 'annually':
            helpText.textContent = 'Z.B. 1 für "jährlich", 2 für "alle 2 Jahre"';
            break;
        case 'hours':
            helpText.textContent = 'Z.B. 500 für "alle 500 Betriebsstunden"';
            break;
        case 'cycles':
            helpText.textContent = 'Z.B. 1000 für "alle 1000 Zyklen"';
            break;
        case 'kilometers':
            helpText.textContent = 'Z.B. 10000 für "alle 10.000 km"';
            break;
        default:
            helpText.textContent = 'Z.B. 30 für "alle 30 [Einheiten]"';
    }
});
</script>
<?= $this->endSection() ?>