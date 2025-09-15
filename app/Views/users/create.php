<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person-plus me-2"></i>Neuen Benutzer erstellen</h2>
            <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Zurück zur Liste
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-fill me-2"></i>Benutzerinformationen
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

                <?php if (session('error')): ?>
                    <div class="alert alert-danger">
                        <?= esc(session('error')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('users') ?>" method="POST">
                    <?= csrf_field() ?>

                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-person me-2"></i>Grundinformationen
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Benutzername <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username"
                                   value="<?= old('username') ?>" required>
                            <div class="form-text">Eindeutiger Benutzername für die Anmeldung</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-Mail-Adresse <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= old('email') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Vorname <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="<?= old('first_name') ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Nachname <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="<?= old('last_name') ?>" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="bi bi-lock me-2"></i>Anmeldedaten
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Passwort <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Mindestens 6 Zeichen</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">Passwort bestätigen <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                    </div>

                    <!-- Role and Department -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="bi bi-shield-check me-2"></i>Rolle und Abteilung
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Rolle <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Rolle auswählen</option>
                                <?php foreach ($roles as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('role') === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Abteilung</label>
                            <select class="form-select" id="department" name="department">
                                <option value="">Abteilung auswählen</option>
                                <?php foreach ($departments as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('department') === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="bi bi-telephone me-2"></i>Kontaktinformationen
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Telefonnummer</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="<?= old('phone') ?>">
                            <div class="form-text">Für interne Kommunikation</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       value="1" <?= old('is_active') ? 'checked' : 'checked' ?>>
                                <label class="form-check-label" for="is_active">
                                    Benutzer ist aktiv
                                </label>
                                <div class="form-text">Aktive Benutzer können sich anmelden</div>
                            </div>
                        </div>
                    </div>

                    <!-- Role Descriptions -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="bi bi-info-circle me-2"></i>Rollenbeschreibungen:</h6>
                        <ul class="mb-0">
                            <li><strong>Administrator:</strong> Vollzugriff auf alle Funktionen</li>
                            <li><strong>Manager:</strong> Kann Arbeitsaufträge verwalten und Berichte erstellen</li>
                            <li><strong>Techniker:</strong> Kann Arbeitsaufträge bearbeiten und Anlagen verwalten</li>
                            <li><strong>Betrachter:</strong> Nur Lesezugriff auf alle Daten</li>
                        </ul>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Abbrechen
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Benutzer erstellen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Role Information Sidebar -->
    <div class="col-lg-4 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-question-circle me-2"></i>Hilfe
                </h6>
            </div>
            <div class="card-body">
                <h6>Benutzerrichtlinien:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Verwenden Sie sichere Passwörter
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Eindeutige Benutzernamen wählen
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Korrekte E-Mail-Adressen angeben
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Passende Rollen zuweisen
                    </li>
                </ul>

                <h6 class="mt-4">Sicherheitshinweise:</h6>
                <div class="alert alert-warning">
                    <small>
                        <i class="bi bi-shield-exclamation me-1"></i>
                        Geben Sie Administratorrechte nur an vertrauenswürdige Personen.
                        Überprüfen Sie regelmäßig die Benutzerberechtigungen.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');

    function validatePasswords() {
        if (password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Passwörter stimmen nicht überein');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }

    password.addEventListener('input', validatePasswords);
    passwordConfirm.addEventListener('input', validatePasswords);

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Real-time validation feedback
    const inputs = form.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });

    // Username availability check (optional)
    const usernameInput = document.getElementById('username');
    let usernameTimeout;

    usernameInput.addEventListener('input', function() {
        clearTimeout(usernameTimeout);
        const username = this.value.trim();

        if (username.length >= 3) {
            usernameTimeout = setTimeout(() => {
                // Here you could add an AJAX call to check username availability
                console.log('Checking username availability for:', username);
            }, 500);
        }
    });
});
</script>
<?= $this->endSection() ?>