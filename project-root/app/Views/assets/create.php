<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Neue Anlage erstellen
                </h5>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <h6>Bitte korrigieren Sie die folgenden Fehler:</h6>
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('assets') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <!-- Grunddaten -->
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Anlagenname <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= old('name') ?>" required>
                            <div class="form-text">Eindeutiger Name für die Anlage</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="asset_number" class="form-label">Anlagennummer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="asset_number" name="asset_number" 
                                   value="<?= old('asset_number') ?>" required>
                            <div class="form-text">Eindeutige Nummer, z.B. A001</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Anlagentyp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="type" name="type" 
                                   value="<?= old('type') ?>" required 
                                   placeholder="z.B. Maschine, Fahrzeug, Gebäude">
                            <div class="form-text">Art der Anlage oder des Geräts</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Standort <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?= old('location') ?>" required 
                                   placeholder="z.B. Halle 1, Raum 205">
                            <div class="form-text">Physischer Standort der Anlage</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Anlagenstatus <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Status wählen...</option>
                                <option value="operational" <?= old('status') === 'operational' ? 'selected' : '' ?>>Betriebsbereit</option>
                                <option value="maintenance" <?= old('status') === 'maintenance' ? 'selected' : '' ?>>Wartung</option>
                                <option value="out_of_order" <?= old('status') === 'out_of_order' ? 'selected' : '' ?>>Außer Betrieb</option>
                                <option value="decommissioned" <?= old('status') === 'decommissioned' ? 'selected' : '' ?>>Stillgelegt</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
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

                    <!-- Herstellerinformationen -->
                    <h6 class="mb-3 mt-4">Herstellerinformationen</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="manufacturer" class="form-label">Hersteller</label>
                            <input type="text" class="form-control" id="manufacturer" name="manufacturer" 
                                   value="<?= old('manufacturer') ?>" 
                                   placeholder="z.B. Siemens, ABB">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="model" class="form-label">Modell</label>
                            <input type="text" class="form-control" id="model" name="model" 
                                   value="<?= old('model') ?>" 
                                   placeholder="z.B. S7-1200">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="serial_number" class="form-label">Seriennummer</label>
                            <input type="text" class="form-control" id="serial_number" name="serial_number" 
                                   value="<?= old('serial_number') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="installation_date" class="form-label">Installationsdatum</label>
                            <input type="date" class="form-control" id="installation_date" name="installation_date" 
                                   value="<?= old('installation_date') ?>">
                            <div class="form-text">Ursprüngliches Installationsdatum</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="purchase_price" class="form-label">Anschaffungspreis</label>
                            <div class="input-group">
                                <span class="input-group-text">CHF</span>
                                <input type="number" class="form-control" id="purchase_price" name="purchase_price" 
                                       value="<?= old('purchase_price') ?>" step="0.01" min="0">
                            </div>
                            <div class="form-text">Ursprünglicher Anschaffungspreis</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= old('description') ?></textarea>
                        <div class="form-text">Detaillierte Beschreibung der Anlage</div>
                    </div>


                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('assets') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Zurück
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Anlage erstellen
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
// Auto-Generierung der Anlagennummer
document.getElementById('name').addEventListener('blur', function() {
    const name = this.value.trim();
    const assetNumberField = document.getElementById('asset_number');
    
    if (name && !assetNumberField.value) {
        // Einfache Auto-Generierung
        let prefix = '';
        const words = name.split(' ');
        
        // Erste 3 Buchstaben jedes Wortes
        words.forEach(word => {
            if (word.length > 0) {
                prefix += word.charAt(0).toUpperCase();
            }
        });
        
        // Zufällige Nummer anhängen
        const randomNum = String(Math.floor(Math.random() * 999) + 1).padStart(3, '0');
        assetNumberField.value = prefix + '-' + randomNum;
    }
});

// Status-abhängige Logik
document.getElementById('status').addEventListener('change', function() {
    const status = this.value;
    const priorityField = document.getElementById('priority');
    
    if (status === 'out_of_order') {
        priorityField.value = 'critical';
    } else if (status === 'maintenance') {
        priorityField.value = 'high';
    } else if (status === 'operational' && !priorityField.value) {
        priorityField.value = 'medium';
    }
});

// Validierung vor dem Absenden
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const assetNumber = document.getElementById('asset_number').value.trim();
    const type = document.getElementById('type').value.trim();
    const location = document.getElementById('location').value.trim();
    const status = document.getElementById('status').value;
    const priority = document.getElementById('priority').value;
    
    if (!name || !assetNumber || !type || !location || !status || !priority) {
        e.preventDefault();
        alert('Bitte füllen Sie alle Pflichtfelder aus.');
        return false;
    }
    
    // Anlagennummer Format prüfen
    if (!assetNumber.match(/^[A-Z0-9\-]+$/)) {
        e.preventDefault();
        alert('Die Anlagennummer darf nur Großbuchstaben, Zahlen und Bindestriche enthalten.');
        document.getElementById('asset_number').focus();
        return false;
    }
});
</script>
<?= $this->endSection() ?>
