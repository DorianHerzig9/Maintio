<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div></div>
            <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>Neuer Benutzer
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['total'] ?></div>
                        <div class="small">Gesamt Benutzer</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-people display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['active'] ?></div>
                        <div class="small">Aktive Benutzer</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-person-check display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['inactive'] ?></div>
                        <div class="small">Inaktive Benutzer</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-person-x display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= count($stats['roles']) ?></div>
                        <div class="small">Rollen</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-shield-check display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>Alle Benutzer
                </h5>
            </div>
            <div class="card-body">
                <?php if (session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= esc(session('success')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= esc(session('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th>Benutzername</th>
                                <th>Name</th>
                                <th>E-Mail</th>
                                <th>Rolle</th>
                                <th>Abteilung</th>
                                <th>Status</th>
                                <th>Letzter Login</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="bi bi-person text-white"></i>
                                            </div>
                                            <?= esc($user['username']) ?>
                                        </div>
                                    </td>
                                    <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= getRoleBadgeColor($user['role']) ?>">
                                            <?= esc($user['role_name']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($user['department'] ?? '-') ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox"
                                                   data-user-id="<?= $user['id'] ?>"
                                                   <?= $user['is_active'] ? 'checked' : '' ?>>
                                            <label class="form-check-label">
                                                <?= $user['is_active'] ? 'Aktiv' : 'Inaktiv' ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($user['last_login']): ?>
                                            <?= date('d.m.Y H:i', strtotime($user['last_login'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Nie</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('users/' . $user['id']) ?>"
                                               class="btn btn-sm btn-outline-info" title="Anzeigen">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('users/' . $user['id'] . '/edit') ?>"
                                               class="btn btn-sm btn-outline-primary" title="Bearbeiten">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger delete-user"
                                                    data-user-id="<?= $user['id'] ?>"
                                                    data-user-name="<?= esc($user['username']) ?>"
                                                    title="Löschen">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-person-x display-4 d-block mb-2"></i>
                                        Keine Benutzer gefunden
                                        <br>
                                        <a href="<?= base_url('users/create') ?>" class="btn btn-primary btn-sm mt-2">
                                            Ersten Benutzer erstellen
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Delete user functionality
    const deleteButtons = document.querySelectorAll('.delete-user');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteForm = document.getElementById('deleteForm');
    const deleteUserName = document.getElementById('deleteUserName');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            deleteUserName.textContent = userName;
            deleteForm.action = `<?= base_url('users') ?>/${userId}`;
            deleteModal.show();
        });
    });

    // Status toggle functionality
    const statusToggles = document.querySelectorAll('.status-toggle');
    statusToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const label = this.nextElementSibling;

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
                    label.textContent = data.status ? 'Aktiv' : 'Inaktiv';

                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.table-responsive'));

                    // Auto dismiss after 3 seconds
                    setTimeout(() => {
                        bootstrap.Alert.getInstance(alertDiv).close();
                    }, 3000);
                } else {
                    // Revert toggle state
                    this.checked = !this.checked;
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert toggle state
                this.checked = !this.checked;
                alert('Fehler beim Ändern des Status');
            });
        });
    });

    // Initialize DataTable if available
    if (typeof DataTable !== 'undefined') {
        new DataTable('#usersTable', {
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/de-DE.json'
            }
        });
    }
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