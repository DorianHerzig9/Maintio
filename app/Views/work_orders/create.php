<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Neuen Arbeitsauftrag erstellen
                </h5>
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
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <strong>Fehler:</strong> <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('work-orders') ?>" method="POST" id="work-order-form">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label">Titel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= old('title') ?>" required>
                            <div class="form-text">Kurze, aussagekräftige Beschreibung des Arbeitsauftrags</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="priority" class="form-label">Priorität <span class="text-danger">*</span></label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="">Priorität wählen...</option>
                                <option value="low" <?= old('priority') === 'low' ? 'selected' : '' ?>>Niedrig</option>
                                <option value="medium" <?= old('priority') === 'medium' ? 'selected' : '' ?>>Mittel</option>
                                <option value="high" <?= old('priority') === 'high' ? 'selected' : '' ?>>Hoch</option>
                                <option value="critical" <?= old('priority') === 'critical' ? 'selected' : '' ?>>Kritisch</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Typ <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Typ wählen...</option>
                                <option value="instandhaltung" <?= old('type') === 'instandhaltung' ? 'selected' : '' ?>>Instandhaltung</option>
                                <option value="instandsetzung" <?= old('type') === 'instandsetzung' ? 'selected' : '' ?>>Instandsetzung</option>
                                <option value="inspektion" <?= old('type') === 'inspektion' ? 'selected' : '' ?>>Inspektion</option>
                                <option value="notfall" <?= old('type') === 'notfall' ? 'selected' : '' ?>>Notfall</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="asset_id" class="form-label">Anlage</label>
                            <select class="form-select" id="asset_id" name="asset_id">
                                <option value="">Keine Anlage zugeordnet</option>
                                <?php foreach ($assets as $asset): ?>
                                    <option value="<?= $asset['id'] ?>" <?= old('asset_id') == $asset['id'] ? 'selected' : '' ?>>
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
                                    <option value="<?= $user['id'] ?>" <?= old('assigned_user_id') == $user['id'] ? 'selected' : '' ?>>
                                        <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> (<?= esc($user['username']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="estimated_duration" class="form-label">Geschätzte Dauer (Minuten)</label>
                            <input type="number" class="form-control" id="estimated_duration" name="estimated_duration" 
                                   value="<?= old('estimated_duration') ?>" min="1">
                            <div class="form-text">Geschätzte Bearbeitungszeit in Minuten</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="scheduled_date" class="form-label">Fälligkeitsdatum <i class="bi bi-calendar-event text-primary"></i></label>
                        <input type="datetime-local" class="form-control" id="scheduled_date" name="scheduled_date"
                               value="<?= old('scheduled_date') ?>">
                        <div class="form-text">Bis wann muss der Arbeitsauftrag abgeschlossen sein? <span class="text-danger">Überfällige Aufträge werden rot markiert.</span></div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= old('description') ?></textarea>
                        <div class="form-text">Detaillierte Beschreibung der durchzuführenden Arbeiten</div>
                    </div>

                    <!-- Komponenten -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label mb-0">Komponenten (Bauteile)</label>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addComponent()">
                                <i class="bi bi-plus me-1"></i>Komponente hinzufügen
                            </button>
                        </div>
                        <div class="form-text mb-3">Fügen Sie die zu bearbeitenden Komponenten hinzu. Diese können später während der Bearbeitung abgehakt werden.</div>
                        
                        <div id="components-container">
                            <!-- Komponenten werden hier hinzugefügt -->
                        </div>
                        
                        <!-- Template für neue Komponenten -->
                        <div id="component-template" style="display: none;">
                            <div class="component-item border rounded p-3 mb-2 bg-light">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">TAG-Nummer</label>
                                                <input type="text" class="form-control" name="components[INDEX][kks_number]"
                                                       placeholder="z.B. FBD-001">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Komponentenname</label>
                                                <input type="text" class="form-control" name="components[INDEX][component_name]" 
                                                       placeholder="z.B. Förderband Eingang">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Beschreibung</label>
                                                <input type="text" class="form-control" name="components[INDEX][description]" 
                                                       placeholder="z.B. Lager schmieren, Riemen prüfen">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm ms-2" onclick="removeComponent(this)" title="Entfernen">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('work-orders') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Zurück
                        </a>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="bi bi-check-circle me-1"></i>Arbeitsauftrag erstellen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
console.log('Create form page loaded and ready');

// Auto-Ausfüllen basierend auf Priorität
document.getElementById('priority').addEventListener('change', function() {
    const priority = this.value;
    const typeSelect = document.getElementById('type');
    
    if (priority === 'critical') {
        typeSelect.value = 'notfall';
    }
});

// Auto-Ausfüllen basierend auf Typ
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const estimatedDuration = document.getElementById('estimated_duration');
    
    // Vorgeschlagene Dauern basierend auf Typ
    const suggestions = {
        'instandhaltung': 120,
        'instandsetzung': 180,
        'notfall': 240,
        'inspektion': 60
    };
    
    if (suggestions[type] && !estimatedDuration.value) {
        estimatedDuration.value = suggestions[type];
    }
});

// Komponenten Management
let componentIndex = 0;

function addComponent() {
    const template = document.getElementById('component-template');
    if (!template) {
        console.error('Component template not found');
        return;
    }
    
    const templateContent = template.innerHTML;
    const newComponent = templateContent.replace(/INDEX/g, componentIndex);
    
    const container = document.getElementById('components-container');
    if (!container) {
        console.error('Components container not found');
        return;
    }
    
    const div = document.createElement('div');
    div.innerHTML = newComponent;
    
    // Append the component
    const componentElement = div.firstElementChild;
    container.appendChild(componentElement);
    
    componentIndex++;
    console.log('Added component with index:', componentIndex - 1);
}

function removeComponent(button) {
    if (button && button.closest) {
        button.closest('.component-item').remove();
        console.log('Removed component');
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('work-order-form');
    const submitBtn = document.getElementById('submit-btn');
    
    console.log('Form element found:', form);
    console.log('Submit button found:', submitBtn);
    
    // Form submission logging (for debugging)
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitting...');
            // Don't prevent submission - let it go through
        });
    }
    
    console.log('Page initialized successfully');
});
</script>
<?= $this->endSection() ?>
