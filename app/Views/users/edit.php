<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-pencil me-2"></i>Benutzer bearbeiten</h2>
            <div>
                <a href="<?= base_url('users/' . $user['id']) ?>" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-eye me-1"></i>Anzeigen
                </a>
                <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Zurück zur Liste
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-fill me-2"></i>Benutzerinformationen bearbeiten
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

                <form action="<?= base_url('users/' . $user['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">

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
                                   value="<?= old('username', $user['username']) ?>" required>
                            <div class="form-text">Eindeutiger Benutzername für die Anmeldung</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-Mail-Adresse <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= old('email', $user['email']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Vorname <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="<?= old('first_name', $user['first_name']) ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Nachname <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="<?= old('last_name', $user['last_name']) ?>" required>
                        </div>
                    </div>

                    <!-- Password (Optional) -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="bi bi-lock me-2"></i>Passwort ändern
                                <small class="text-muted">(optional)</small>
                            </h6>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Lassen Sie diese Felder leer, wenn Sie das Passwort nicht ändern möchten.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Neues Passwort</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Mindestens 6 Zeichen</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">Passwort bestätigen</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm">
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
                                    <option value="<?= $value ?>"
                                            <?= old('role', $user['role']) === $value ? 'selected' : '' ?>>
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
                                    <option value="<?= $value ?>"
                                            <?= old('department', $user['department']) === $value ? 'selected' : '' ?>>
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
                                   value="<?= old('phone', $user['phone']) ?>">
                            <div class="form-text">Für interne Kommunikation</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       value="1" <?= old('is_active', $user['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    Benutzer ist aktiv
                                </label>
                                <div class="form-text">Aktive Benutzer können sich anmelden</div>
                            </div>
                        </div>
                    </div>

                    <!-- User Information Display -->
                    <div class="alert alert-light mt-4">
                        <h6><i class="bi bi-info-circle me-2"></i>Benutzerinformationen:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Erstellt:</strong> <?= date('d.m.Y H:i', strtotime($user['created_at'])) ?><br>
                                    <strong>Aktualisiert:</strong> <?= date('d.m.Y H:i', strtotime($user['updated_at'])) ?><br>
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Letzter Login:</strong>
                                    <?php if ($user['last_login']): ?>
                                        <?= date('d.m.Y H:i', strtotime($user['last_login'])) ?>
                                    <?php else: ?>
                                        Nie
                                    <?php endif; ?><br>
                                    <strong>Benutzer-ID:</strong> <?= $user['id'] ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url('users/' . $user['id']) ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Abbrechen
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Änderungen speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Information Sidebar -->
    <div class="col-lg-4 col-xl-6">
        <!-- Current User Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-person-circle me-2"></i>Aktueller Benutzer
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-person text-white h4 mb-0"></i>
                    </div>
                    <div>
                        <h6 class="mb-0"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                        <small class="text-muted">@<?= esc($user['username']) ?></small>
                    </div>
                </div>

                <div class="mb-2">
                    <small class="text-muted">Aktuelle Rolle:</small><br>
                    <span class="badge bg-<?= getRoleBadgeColor($user['role']) ?>">
                        <?= esc($roles[$user['role']] ?? $user['role']) ?>
                    </span>
                </div>

                <div class="mb-2">
                    <small class="text-muted">Status:</small><br>
                    <?php if ($user['is_active']): ?>
                        <span class="badge bg-success">Aktiv</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Inaktiv</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Role Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-shield-check me-2"></i>Rolleninformationen
                </h6>
            </div>
            <div class="card-body">
                <h6>Verfügbare Rollen:</h6>
                <div class="role-info">
                    <div class="mb-3">
                        <span class="badge bg-danger me-2">Administrator</span>
                        <small class="text-muted">Vollzugriff auf alle Funktionen</small>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-warning me-2">Manager</span>
                        <small class="text-muted">Verwaltung und Berichtserstellung</small>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-primary me-2">Techniker</span>
                        <small class="text-muted">Arbeitsaufträge und Anlagen</small>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-secondary me-2">Betrachter</span>
                        <small class="text-muted">Nur Lesezugriff</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-shield-exclamation me-2"></i>Sicherheitshinweise
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <small>
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Wichtig:</strong> Änderungen an Benutzerrollen wirken sich sofort auf die Berechtigung aus.
                    </small>
                </div>

                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        <small>Überprüfen Sie Änderungen sorgfältig</small>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        <small>Verwenden Sie sichere Passwörter</small>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        <small>Deaktivieren Sie ungenutzte Konten</small>
                    </li>
                </ul>
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
        if (password.value && passwordConfirm.value) {
            if (password.value !== passwordConfirm.value) {
                passwordConfirm.setCustomValidity('Passwörter stimmen nicht überein');
            } else {
                passwordConfirm.setCustomValidity('');
            }
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

    // Highlight changed fields
    const originalValues = {};
    inputs.forEach(input => {
        originalValues[input.name] = input.value;

        input.addEventListener('input', function() {
            if (this.value !== originalValues[this.name]) {
                this.style.borderLeft = '3px solid #198754';
            } else {
                this.style.borderLeft = '';
            }
        });
    });

    // Role change warning
    const roleSelect = document.getElementById('role');
    const originalRole = roleSelect.value;

    roleSelect.addEventListener('change', function() {
        if (this.value !== originalRole) {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'alert alert-warning mt-2';
            warningDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Rollenänderung wirkt sich sofort auf Berechtigungen aus.';

            // Remove existing warnings
            const existingWarning = roleSelect.parentNode.querySelector('.alert-warning');
            if (existingWarning) {
                existingWarning.remove();
            }

            roleSelect.parentNode.appendChild(warningDiv);
        }
    });
});

// Helper function for role badge colors
function getRoleBadgeColor(role) {
    switch(role) {
        case 'admin': return 'danger';
        case 'manager': return 'warning';
        case 'technician': return 'primary';
        case 'viewer': return 'secondary';
        default: return 'secondary';
    }
}
</script>
<?= $this->endSection() ?>

<?php
function getRoleBadgeColor($role) {
    switch($role) {
        case 'admin': return 'danger';
        case 'manager': return 'warning';
        case 'technician': return 'primary';
        case 'viewer': return 'secondary';
        default: return 'secondary';
    }
}
?>