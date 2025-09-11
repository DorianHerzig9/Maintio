<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Anlage bearbeiten
                    </h5>
                    <small class="text-muted"><?= esc($asset['asset_number']) ?></small>
                </div>
                <a href="<?= base_url('assets/' . $asset['id']) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Zurück
                </a>
            </div>
            <div class="card-body">
                <?php if (session('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <?php if (session('message')): ?>
                    <div class="alert alert-success">
                        <?= esc(session('message')) ?>
                    </div>
                <?php endif ?>

                <form action="<?= base_url('assets/' . $asset['id']) ?>" method="post" id="editAssetForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">

                    <div class="row">
                        <!-- Grunddaten -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= esc($asset['name']) ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Typ <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Typ wählen...</option>
                                    <option value="Maschine" <?= $asset['type'] === 'Maschine' ? 'selected' : '' ?>>Maschine</option>
                                    <option value="Fahrzeug" <?= $asset['type'] === 'Fahrzeug' ? 'selected' : '' ?>>Fahrzeug</option>
                                    <option value="Gebäude" <?= $asset['type'] === 'Gebäude' ? 'selected' : '' ?>>Gebäude</option>
                                    <option value="IT-Equipment" <?= $asset['type'] === 'IT-Equipment' ? 'selected' : '' ?>>IT-Equipment</option>
                                    <option value="Werkzeug" <?= $asset['type'] === 'Werkzeug' ? 'selected' : '' ?>>Werkzeug</option>
                                    <option value="Infrastruktur" <?= $asset['type'] === 'Infrastruktur' ? 'selected' : '' ?>>Infrastruktur</option>
                                    <option value="Sonstiges" <?= $asset['type'] === 'Sonstiges' ? 'selected' : '' ?>>Sonstiges</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Standort <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?= esc($asset['location']) ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="operational" <?= $asset['status'] === 'operational' ? 'selected' : '' ?>>
                                        Betriebsbereit
                                    </option>
                                    <option value="maintenance" <?= $asset['status'] === 'maintenance' ? 'selected' : '' ?>>
                                        Wartung
                                    </option>
                                    <option value="out_of_order" <?= $asset['status'] === 'out_of_order' ? 'selected' : '' ?>>
                                        Außer Betrieb
                                    </option>
                                    <option value="decommissioned" <?= $asset['status'] === 'decommissioned' ? 'selected' : '' ?>>
                                        Stillgelegt
                                    </option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="priority" class="form-label">Priorität <span class="text-danger">*</span></label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="low" <?= $asset['priority'] === 'low' ? 'selected' : '' ?>>Niedrig</option>
                                    <option value="medium" <?= $asset['priority'] === 'medium' ? 'selected' : '' ?>>Mittel</option>
                                    <option value="high" <?= $asset['priority'] === 'high' ? 'selected' : '' ?>>Hoch</option>
                                    <option value="critical" <?= $asset['priority'] === 'critical' ? 'selected' : '' ?>>Kritisch</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Herstellerdaten -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="manufacturer" class="form-label">Hersteller</label>
                                <input type="text" class="form-control" id="manufacturer" name="manufacturer" 
                                       value="<?= esc($asset['manufacturer']) ?>">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="model" class="form-label">Modell</label>
                                <input type="text" class="form-control" id="model" name="model" 
                                       value="<?= esc($asset['model']) ?>">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="serial_number" class="form-label">Seriennummer</label>
                                <input type="text" class="form-control" id="serial_number" name="serial_number" 
                                       value="<?= esc($asset['serial_number']) ?>">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="installation_date" class="form-label">Installationsdatum</label>
                                <input type="date" class="form-control" id="installation_date" name="installation_date" 
                                       value="<?= $asset['installation_date'] ?>">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="purchase_price" class="form-label">Anschaffungspreis (CHF)</label>
                                <div class="input-group">
                                    <span class="input-group-text">CHF</span>
                                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" 
                                           value="<?= $asset['purchase_price'] ?>" step="0.01" min="0">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Beschreibung -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Detaillierte Beschreibung der Anlage..."><?= esc($asset['description']) ?></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Buttons -->
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Änderungen speichern
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" 
                                    onclick="window.location.href='<?= base_url('assets/' . $asset['id']) ?>'">
                                Abbrechen
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                <i class="bi bi-trash me-1"></i>Anlage löschen
                            </button>
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editAssetForm');
    
    // Form validation
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        event.stopPropagation();
        
        if (validateForm()) {
            this.submit();
        }
    });
    
    function validateForm() {
        let isValid = true;
        const formControls = form.querySelectorAll('.form-control, .form-select');
        
        formControls.forEach(function(control) {
            const feedback = control.parentNode.querySelector('.invalid-feedback');
            control.classList.remove('is-invalid');
            
            // Required field validation
            if (control.hasAttribute('required') && !control.value.trim()) {
                showError(control, 'Dieses Feld ist erforderlich.');
                isValid = false;
                return;
            }
            
            // Name validation
            if (control.id === 'name' && control.value.trim().length < 2) {
                showError(control, 'Name muss mindestens 2 Zeichen lang sein.');
                isValid = false;
            }
            
            // Location validation
            if (control.id === 'location' && control.value.trim().length < 2) {
                showError(control, 'Standort muss mindestens 2 Zeichen lang sein.');
                isValid = false;
            }
            
            // Price validation
            if (control.id === 'purchase_price' && control.value && parseFloat(control.value) < 0) {
                showError(control, 'Preis kann nicht negativ sein.');
                isValid = false;
            }
            
            // Installation date validation
            if (control.id === 'installation_date' && control.value) {
                const installDate = new Date(control.value);
                const today = new Date();
                
                if (installDate > today) {
                    showError(control, 'Installationsdatum kann nicht in der Zukunft liegen.');
                    isValid = false;
                }
            }
        });
        
        return isValid;
    }
    
    function showError(control, message) {
        control.classList.add('is-invalid');
        const feedback = control.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
        }
    }
    
    // Auto-save draft (optional)
    let saveTimeout;
    form.addEventListener('input', function() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(function() {
            // Could implement auto-save to localStorage here
            console.log('Form data auto-saved');
        }, 2000);
    });
});

function confirmDelete() {
    if (confirm('Möchten Sie diese Anlage wirklich löschen?\n\nHinweis: Diese Aktion kann nicht rückgängig gemacht werden.')) {
        // Erstelle und sende DELETE-Request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('assets/' . $asset['id']) ?>';
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        
        form.appendChild(methodInput);
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>
