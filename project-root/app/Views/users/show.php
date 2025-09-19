<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person me-2"></i>Benutzer Details</h2>
            <div>
                <a href="<?= base_url('users/' . $user['id'] . '/edit') ?>" class="btn btn-primary me-2">
                    <i class="bi bi-pencil me-1"></i>Bearbeiten
                </a>
                <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Zurück zur Liste
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- User Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-fill me-2"></i>Benutzerinformationen
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Benutzername</label>
                            <p class="form-control-plaintext"><?= esc($user['username']) ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Vorname</label>
                            <p class="form-control-plaintext"><?= esc($user['first_name']) ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nachname</label>
                            <p class="form-control-plaintext"><?= esc($user['last_name']) ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Rolle</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-<?= getRoleBadgeColor($user['role']) ?> fs-6">
                                    <?= esc($role_name) ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">E-Mail-Adresse</label>
                            <p class="form-control-plaintext">
                                <a href="mailto:<?= esc($user['email']) ?>"><?= esc($user['email']) ?></a>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Telefonnummer</label>
                            <p class="form-control-plaintext">
                                <?php if ($user['phone']): ?>
                                    <a href="tel:<?= esc($user['phone']) ?>"><?= esc($user['phone']) ?></a>
                                <?php else: ?>
                                    <span class="text-muted">Nicht angegeben</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Abteilung</label>
                            <p class="form-control-plaintext">
                                <?= esc($user['department'] ?? 'Nicht zugewiesen') ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <?php if ($user['is_active']): ?>
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle me-1"></i>Aktiv
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger fs-6">
                                        <i class="bi bi-x-circle me-1"></i>Inaktiv
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Aktivitätsinformationen
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Erstellt am</label>
                            <p class="form-control-plaintext">
                                <?= date('d.m.Y H:i', strtotime($user['created_at'])) ?> Uhr
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Letzter Login</label>
                            <p class="form-control-plaintext">
                                <?php if ($user['last_login']): ?>
                                    <?= date('d.m.Y H:i', strtotime($user['last_login'])) ?> Uhr
                                <?php else: ?>
                                    <span class="text-muted">Noch nie angemeldet</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Zuletzt aktualisiert</label>
                            <p class="form-control-plaintext">
                                <?= date('d.m.Y H:i', strtotime($user['updated_at'])) ?> Uhr
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Benutzer-ID</label>
                            <p class="form-control-plaintext">
                                <code><?= esc($user['id']) ?></code>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>Schnellaktionen
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('users/' . $user['id'] . '/edit') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Benutzer bearbeiten
                    </a>

                    <?php if ($user['is_active']): ?>
                        <button class="btn btn-outline-warning" onclick="toggleUserStatus(<?= $user['id'] ?>)">
                            <i class="bi bi-pause-circle me-2"></i>Deaktivieren
                        </button>
                    <?php else: ?>
                        <button class="btn btn-outline-success" onclick="toggleUserStatus(<?= $user['id'] ?>)">
                            <i class="bi bi-play-circle me-2"></i>Aktivieren
                        </button>
                    <?php endif; ?>

                    <button class="btn btn-outline-info" onclick="resetPassword(<?= $user['id'] ?>)">
                        <i class="bi bi-key me-2"></i>Passwort zurücksetzen
                    </button>

                    <hr>

                    <button class="btn btn-outline-danger" onclick="deleteUser(<?= $user['id'] ?>, '<?= esc($user['username']) ?>')">
                        <i class="bi bi-trash me-2"></i>Benutzer löschen
                    </button>
                </div>
            </div>
        </div>

        <!-- Role Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-shield-check me-2"></i>Rollenberechtigung
                </h6>
            </div>
            <div class="card-body">
                <?php
                $roleDescriptions = [
                    'admin' => [
                        'name' => 'Administrator',
                        'description' => 'Vollzugriff auf alle Funktionen und Einstellungen',
                        'permissions' => ['Benutzerverwaltung', 'Systemeinstellungen', 'Alle Berichte', 'Datenverwaltung']
                    ],
                    'manager' => [
                        'name' => 'Manager',
                        'description' => 'Verwaltung von Arbeitsaufträgen und Berichtserstellung',
                        'permissions' => ['Arbeitsaufträge verwalten', 'Berichte erstellen', 'Anlagen einsehen', 'Wartungspläne']
                    ],
                    'technician' => [
                        'name' => 'Techniker',
                        'description' => 'Bearbeitung von Arbeitsaufträgen und Anlagenverwaltung',
                        'permissions' => ['Arbeitsaufträge bearbeiten', 'Anlagen verwalten', 'Wartungen durchführen', 'Statusupdates']
                    ],
                    'viewer' => [
                        'name' => 'Betrachter',
                        'description' => 'Nur Lesezugriff auf alle Daten',
                        'permissions' => ['Daten einsehen', 'Berichte anzeigen', 'Export-Funktionen']
                    ]
                ];

                $roleInfo = $roleDescriptions[$user['role']] ?? null;
                ?>

                <?php if ($roleInfo): ?>
                    <h6><?= $roleInfo['name'] ?></h6>
                    <p class="text-muted"><?= $roleInfo['description'] ?></p>

                    <h6 class="mt-3">Berechtigungen:</h6>
                    <ul class="list-unstyled">
                        <?php foreach ($roleInfo['permissions'] as $permission): ?>
                            <li class="mb-1">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <?= $permission ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contact Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-envelope me-2"></i>Kontakt
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="mailto:<?= esc($user['email']) ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-envelope me-2"></i>E-Mail senden
                    </a>

                    <?php if ($user['phone']): ?>
                        <a href="tel:<?= esc($user['phone']) ?>" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-telephone me-2"></i>Anrufen
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Reset Modal -->
<div class="modal fade" id="passwordResetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Passwort zurücksetzen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Möchten Sie das Passwort für <strong><?= esc($user['username']) ?></strong> zurücksetzen?</p>
                <p class="text-info"><small><i class="bi bi-info-circle me-1"></i>Ein neues temporäres Passwort wird generiert und per E-Mail gesendet.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-primary" onclick="confirmPasswordReset()">Passwort zurücksetzen</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Benutzer löschen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Möchten Sie den Benutzer <strong id="deleteUserName"></strong> wirklich löschen?</p>
                <p class="text-danger"><small><i class="bi bi-exclamation-triangle me-1"></i>Diese Aktion kann nicht rückgängig gemacht werden.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Löschen</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleUserStatus(userId) {
    fetch(`<?= base_url('users') ?>/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh page to show updated status
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Ändern des Status');
    });
}

function resetPassword(userId) {
    const modal = new bootstrap.Modal(document.getElementById('passwordResetModal'));
    modal.show();
}

function confirmPasswordReset() {
    // Here you would implement the password reset functionality
    alert('Passwort-Reset-Funktionalität würde hier implementiert werden');
    bootstrap.Modal.getInstance(document.getElementById('passwordResetModal')).hide();
}

function deleteUser(userId, userName) {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteForm = document.getElementById('deleteForm');
    const deleteUserName = document.getElementById('deleteUserName');

    deleteUserName.textContent = userName;
    deleteForm.action = `<?= base_url('users') ?>/${userId}`;
    deleteModal.show();
}

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