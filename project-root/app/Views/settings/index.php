<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Systemeinstellungen
                </h5>
            </div>
            <div class="card-body">
                <?php if (session('errors')): ?>
                    <div class="alert alert-danger">
                        <h6>Bitte korrigieren Sie folgende Fehler:</h6>
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (session('success')): ?>
                    <div class="alert alert-success">
                        <?= esc(session('success')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                    <div class="alert alert-danger">
                        <?= esc(session('error')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('settings') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <!-- Allgemeine Einstellungen -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-building me-2"></i>
                                Allgemeine Einstellungen
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="app_name" class="form-label">Anwendungsname <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="app_name" name="app_name" 
                                   value="<?= esc($settings['app_name'] ?? '') ?>" required>
                            <div class="form-text">Name der Anwendung (wird im Header angezeigt)</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">Firmenname <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="company_name" name="company_name" 
                                   value="<?= esc($settings['company_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_email" class="form-label">Firmen E-Mail</label>
                            <input type="email" class="form-control" id="company_email" name="company_email" 
                                   value="<?= esc($settings['company_email'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="company_phone" class="form-label">Firmen Telefon</label>
                            <input type="text" class="form-control" id="company_phone" name="company_phone" 
                                   value="<?= esc($settings['company_phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="company_address" class="form-label">Firmenadresse</label>
                        <textarea class="form-control" id="company_address" name="company_address" rows="3"><?= esc($settings['company_address'] ?? '') ?></textarea>
                    </div>

                    <!-- Regionale Einstellungen -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="bi bi-globe me-2"></i>
                                Regionale Einstellungen
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="timezone" class="form-label">Zeitzone <span class="text-danger">*</span></label>
                            <select class="form-select" id="timezone" name="timezone" required>
                                <option value="Europe/Zurich" <?= ($settings['timezone'] ?? '') === 'Europe/Zurich' ? 'selected' : '' ?>>Europe/Zurich</option>
                                <option value="Europe/Berlin" <?= ($settings['timezone'] ?? '') === 'Europe/Berlin' ? 'selected' : '' ?>>Europe/Berlin</option>
                                <option value="Europe/Vienna" <?= ($settings['timezone'] ?? '') === 'Europe/Vienna' ? 'selected' : '' ?>>Europe/Vienna</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="date_format" class="form-label">Datumsformat <span class="text-danger">*</span></label>
                            <select class="form-select" id="date_format" name="date_format" required>
                                <option value="d.m.Y" <?= ($settings['date_format'] ?? '') === 'd.m.Y' ? 'selected' : '' ?>>31.12.2023</option>
                                <option value="Y-m-d" <?= ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>2023-12-31</option>
                                <option value="m/d/Y" <?= ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' ?>>12/31/2023</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="time_format" class="form-label">Zeitformat <span class="text-danger">*</span></label>
                            <select class="form-select" id="time_format" name="time_format" required>
                                <option value="H:i" <?= ($settings['time_format'] ?? '') === 'H:i' ? 'selected' : '' ?>>24:00</option>
                                <option value="h:i A" <?= ($settings['time_format'] ?? '') === 'h:i A' ? 'selected' : '' ?>>12:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="language" class="form-label">Sprache <span class="text-danger">*</span></label>
                            <select class="form-select" id="language" name="language" required>
                                <option value="de" <?= ($settings['language'] ?? 'de') === 'de' ? 'selected' : '' ?>>Deutsch</option>
                                <option value="en" <?= ($settings['language'] ?? '') === 'en' ? 'selected' : '' ?>>English</option>
                            </select>
                        </div>
                    </div>

                    <!-- Wartungseinstellungen -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="bi bi-tools me-2"></i>
                                Wartungseinstellungen
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="maintenance_interval_default" class="form-label">Standard Wartungsintervall (Tage) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="maintenance_interval_default" name="maintenance_interval_default" 
                                   value="<?= esc($settings['maintenance_interval_default'] ?? '30') ?>" min="1" required>
                            <div class="form-text">Standardintervall für neue Wartungspläne</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="notification_email" class="form-label">Benachrichtigungs E-Mail</label>
                            <input type="email" class="form-control" id="notification_email" name="notification_email" 
                                   value="<?= esc($settings['notification_email'] ?? '') ?>">
                            <div class="form-text">E-Mail für Systembenachrichtigungen</div>
                        </div>
                    </div>

                    <!-- Arbeitsaufträge Einstellungen -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Arbeitsaufträge
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="work_order_prefix" class="form-label">Arbeitsauftrag Präfix</label>
                            <input type="text" class="form-control" id="work_order_prefix" name="work_order_prefix" 
                                   value="<?= esc($settings['work_order_prefix'] ?? 'WO-') ?>" maxlength="10">
                            <div class="form-text">Präfix für Arbeitsauftragsnummern (z.B. WO-)</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="asset_prefix" class="form-label">Anlagen Präfix</label>
                            <input type="text" class="form-control" id="asset_prefix" name="asset_prefix" 
                                   value="<?= esc($settings['asset_prefix'] ?? 'A-') ?>" maxlength="10">
                            <div class="form-text">Präfix für Anlagennummern (z.B. A-)</div>
                        </div>
                    </div>

                    <!-- Benachrichtigungen -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="bi bi-bell me-2"></i>
                                Benachrichtigungen
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" 
                                       <?= ($settings['email_notifications'] ?? '1') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="email_notifications">
                                    E-Mail Benachrichtigungen aktivieren
                                </label>
                                <div class="form-text">Automatische E-Mail Benachrichtigungen für wichtige Ereignisse</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto_assign_work_orders" name="auto_assign_work_orders" value="1" 
                                       <?= ($settings['auto_assign_work_orders'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="auto_assign_work_orders">
                                    Arbeitsaufträge automatisch zuweisen
                                </label>
                                <div class="form-text">Neue Arbeitsaufträge automatisch an verfügbare Techniker zuweisen</div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url('/') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Zurück zum Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Einstellungen speichern
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
document.addEventListener('DOMContentLoaded', function() {
    // Form validation feedback
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
    
    // Auto-save indication (optional)
    let saveTimeout;
    form.addEventListener('input', function() {
        clearTimeout(saveTimeout);
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="bi bi-clock me-1"></i>Nicht gespeichert';
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-warning');
        
        saveTimeout = setTimeout(function() {
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Einstellungen speichern';
            submitBtn.classList.remove('btn-warning');
            submitBtn.classList.add('btn-primary');
        }, 3000);
    });
});
</script>
<?= $this->endSection() ?>