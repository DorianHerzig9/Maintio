<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Berichte</a></li>
                        <li class="breadcrumb-item active">Geplante Berichte</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#newScheduledReportModal">
                    <i class="bi bi-plus-circle me-1"></i>Neuen geplanten Bericht erstellen
                </button>
                <a href="<?= base_url('reports') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Zurück zu Berichten
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Scheduled Reports Overview -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= count($scheduledReports) ?></div>
                        <div class="small">Geplante Berichte</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clock display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= count(array_filter($scheduledReports, fn($r) => $r['is_active'])) ?></div>
                        <div class="small">Aktive Berichte</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= count(array_filter($scheduledReports, fn($r) => strtotime($r['next_run']) <= time() + 24*60*60)) ?></div>
                        <div class="small">Fällig heute/morgen</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-exclamation-triangle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= count(array_filter($scheduledReports, fn($r) => $r['last_run'])) ?></div>
                        <div class="small">Erfolgreich ausgeführt</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-graph-up display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scheduled Reports Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>Geplante Berichte verwalten
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($scheduledReports)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="scheduledReportsTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Typ</th>
                                    <th>Zeitplan</th>
                                    <th>Format</th>
                                    <th>Empfänger</th>
                                    <th>Letzte Ausführung</th>
                                    <th>Nächste Ausführung</th>
                                    <th>Status</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($scheduledReports as $report): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= esc($report['name']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= ucfirst($report['report_type']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?= ucfirst($report['schedule_type']) ?>
                                            <?php if ($report['schedule_type'] === 'weekly'): ?>
                                                - <?= ucfirst($report['schedule_value']) ?>
                                            <?php elseif ($report['schedule_type'] === 'monthly'): ?>
                                                - Tag <?= $report['schedule_value'] ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= strtoupper($report['format']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= esc($report['recipients']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($report['last_run']): ?>
                                            <small><?= date('d.m.Y H:i', strtotime($report['last_run'])) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Noch nie</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="<?= strtotime($report['next_run']) <= time() ? 'text-danger fw-bold' : '' ?>">
                                            <?= date('d.m.Y H:i', strtotime($report['next_run'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($report['is_active']): ?>
                                            <span class="badge bg-success">Aktiv</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inaktiv</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="runNow(<?= $report['id'] ?>)" title="Jetzt ausführen">
                                                <i class="bi bi-play-fill"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" onclick="editScheduledReport(<?= $report['id'] ?>)" title="Bearbeiten">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-warning" onclick="toggleActive(<?= $report['id'] ?>, <?= $report['is_active'] ? 'false' : 'true' ?>)" title="<?= $report['is_active'] ? 'Deaktivieren' : 'Aktivieren' ?>">
                                                <i class="bi bi-<?= $report['is_active'] ? 'pause' : 'play' ?>"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteScheduledReport(<?= $report['id'] ?>)" title="Löschen">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-clock display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">Keine geplanten Berichte</h5>
                        <p class="text-muted">Erstellen Sie Ihren ersten geplanten Bericht für automatische Berichtserstellung.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newScheduledReportModal">
                            <i class="bi bi-plus-circle me-1"></i>Ersten geplanten Bericht erstellen
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- New Scheduled Report Modal -->
<div class="modal fade" id="newScheduledReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Neuen geplanten Bericht erstellen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= base_url('reports/create-scheduled') ?>" id="scheduledReportForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="report_name" class="form-label">Berichtsname</label>
                            <input type="text" class="form-control" id="report_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="report_type" class="form-label">Berichtstyp</label>
                            <select class="form-select" id="report_type" name="report_type" required>
                                <?php foreach ($reportTypes as $key => $value): ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="schedule_type" class="form-label">Zeitplan-Typ</label>
                            <select class="form-select" id="schedule_type" name="schedule_type" required>
                                <?php foreach ($scheduleOptions as $key => $value): ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="schedule_value_container">
                            <label for="schedule_value" class="form-label">Zeitplan-Wert</label>
                            <input type="text" class="form-control" id="schedule_value" name="schedule_value" placeholder="z.B. monday, 1, etc.">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="format" class="form-label">Export-Format</label>
                            <select class="form-select" id="format" name="format">
                                <option value="csv">CSV</option>
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="recipients" class="form-label">E-Mail Empfänger</label>
                            <input type="email" class="form-control" id="recipients" name="recipients" placeholder="user@example.com" multiple>
                            <div class="form-text">Mehrere E-Mails mit Komma trennen</div>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="row">
                        <div class="col-12">
                            <h6>Filter (optional)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="filter_status" class="form-label">Status</label>
                                    <select class="form-select" id="filter_status" name="filters[status]">
                                        <option value="">Alle Status</option>
                                        <option value="open">Offen</option>
                                        <option value="in_progress">In Bearbeitung</option>
                                        <option value="completed">Abgeschlossen</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="filter_priority" class="form-label">Priorität</label>
                                    <select class="form-select" id="filter_priority" name="filters[priority]">
                                        <option value="">Alle Prioritäten</option>
                                        <option value="low">Niedrig</option>
                                        <option value="medium">Mittel</option>
                                        <option value="high">Hoch</option>
                                        <option value="urgent">Dringend</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Geplanten Bericht erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Help Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h6><i class="bi bi-question-circle me-2"></i>Hilfe zu geplanten Berichten</h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="small">Zeitplan-Optionen:</h6>
                        <ul class="small text-muted mb-0">
                            <li><strong>Täglich:</strong> Jeden Tag zur gleichen Zeit</li>
                            <li><strong>Wöchentlich:</strong> Jeden Wochentag (monday, tuesday, etc.)</li>
                            <li><strong>Monatlich:</strong> Am X. Tag des Monats (1-31)</li>
                            <li><strong>Vierteljährlich:</strong> Alle 3 Monate</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="small">Funktionen:</h6>
                        <ul class="small text-muted mb-0">
                            <li>Automatische E-Mail-Versendung</li>
                            <li>Verschiedene Export-Formate</li>
                            <li>Flexible Filter-Optionen</li>
                            <li>Ein/Ausschalten von Berichten</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable if available
    if (typeof DataTable !== 'undefined' && document.getElementById('scheduledReportsTable')) {
        new DataTable('#scheduledReportsTable', {
            responsive: true,
            pageLength: 10,
            order: [[6, 'asc']], // Sort by next run
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/de-DE.json'
            }
        });
    }

    // Handle schedule type changes
    document.getElementById('schedule_type').addEventListener('change', function() {
        const scheduleValue = document.getElementById('schedule_value');
        const container = document.getElementById('schedule_value_container');

        switch(this.value) {
            case 'daily':
                scheduleValue.style.display = 'none';
                container.style.display = 'none';
                break;
            case 'weekly':
                scheduleValue.style.display = 'block';
                container.style.display = 'block';
                scheduleValue.placeholder = 'z.B. monday, tuesday, wednesday...';
                break;
            case 'monthly':
                scheduleValue.style.display = 'block';
                container.style.display = 'block';
                scheduleValue.placeholder = 'Tag des Monats (1-31)';
                break;
            case 'quarterly':
                scheduleValue.style.display = 'none';
                container.style.display = 'none';
                break;
        }
    });
});

function runNow(reportId) {
    if (confirm('Möchten Sie diesen Bericht jetzt ausführen?')) {
        // AJAX call to run report immediately
        alert('Bericht wird ausgeführt... (Feature in Entwicklung)');
    }
}

function editScheduledReport(reportId) {
    alert('Bearbeiten-Funktion in Entwicklung');
}

function toggleActive(reportId, newStatus) {
    const action = newStatus === 'true' ? 'aktivieren' : 'deaktivieren';
    if (confirm(`Möchten Sie diesen Bericht ${action}?`)) {
        // AJAX call to toggle status
        alert(`Bericht wird ${action}... (Feature in Entwicklung)`);
    }
}

function deleteScheduledReport(reportId) {
    if (confirm('Möchten Sie diesen geplanten Bericht wirklich löschen?')) {
        // AJAX call to delete report
        alert('Bericht wird gelöscht... (Feature in Entwicklung)');
    }
}
</script>
<?= $this->endSection() ?>