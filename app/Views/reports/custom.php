<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Berichte</a></li>
                        <li class="breadcrumb-item active">Benutzerdefiniert</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="<?= base_url('reports') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Zurück zu Berichten
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Report Builder Form -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear-fill me-2"></i>Berichts-Generator
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= base_url('reports/generate-custom') ?>" id="customReportForm">
                    <div class="row">
                        <!-- Report Name -->
                        <div class="col-md-6 mb-3">
                            <label for="report_name" class="form-label">Berichtsname</label>
                            <input type="text" class="form-control" id="report_name" name="report_name"
                                   placeholder="Mein benutzerdefinierter Bericht" required>
                        </div>

                        <!-- Report Type -->
                        <div class="col-md-6 mb-3">
                            <label for="report_type" class="form-label">Berichtstyp</label>
                            <select class="form-select" id="report_type" name="report_type" required>
                                <option value="">Bitte wählen...</option>
                                <?php foreach ($reportTypes as $key => $value): ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Fields Selection -->
                    <div class="row" id="fieldsSection" style="display: none;">
                        <div class="col-12 mb-3">
                            <label class="form-label">Anzuzeigende Felder</label>
                            <div id="fieldsList" class="border rounded p-3">
                                <!-- Fields will be populated via JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Filter</label>
                            <div id="filtersSection">
                                <!-- Date Range Filter -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="date_from" class="form-label">Von Datum</label>
                                        <input type="date" class="form-control" id="date_from" name="filters[date_from]">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_to" class="form-label">Bis Datum</label>
                                        <input type="date" class="form-control" id="date_to" name="filters[date_to]">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="status_filter" class="form-label">Status</label>
                                        <select class="form-select" id="status_filter" name="filters[status]">
                                            <option value="">Alle Status</option>
                                            <option value="open">Offen</option>
                                            <option value="in_progress">In Bearbeitung</option>
                                            <option value="completed">Abgeschlossen</option>
                                            <option value="cancelled">Storniert</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="priority_filter" class="form-label">Priorität</label>
                                        <select class="form-select" id="priority_filter" name="filters[priority]">
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

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-play-fill me-1"></i>Bericht generieren
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Zurücksetzen
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Help Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h6><i class="bi bi-question-circle me-2"></i>Hilfe zu benutzerdefinierten Berichten</h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="small">Schritte:</h6>
                        <ol class="small text-muted mb-0">
                            <li>Berichtsname eingeben</li>
                            <li>Berichtstyp auswählen</li>
                            <li>Gewünschte Felder markieren</li>
                            <li>Filter setzen (optional)</li>
                            <li>Bericht generieren</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6 class="small">Tipps:</h6>
                        <ul class="small text-muted mb-0">
                            <li>Weniger Felder = schnellere Generierung</li>
                            <li>Filter reduzieren die Datenmenge</li>
                            <li>Kombinierte Berichte benötigen mehr Zeit</li>
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
const availableFields = <?= json_encode($fields) ?>;

document.getElementById('report_type').addEventListener('change', function() {
    const reportType = this.value;
    const fieldsSection = document.getElementById('fieldsSection');
    const fieldsList = document.getElementById('fieldsList');

    if (reportType && availableFields[reportType]) {
        fieldsSection.style.display = 'block';
        fieldsList.innerHTML = '';

        Object.keys(availableFields[reportType]).forEach(fieldKey => {
            const fieldName = availableFields[reportType][fieldKey];
            const checkboxDiv = document.createElement('div');
            checkboxDiv.className = 'form-check form-check-inline';
            checkboxDiv.innerHTML = `
                <input class="form-check-input" type="checkbox" id="field_${fieldKey}" name="fields[]" value="${fieldKey}" checked>
                <label class="form-check-label" for="field_${fieldKey}">
                    ${fieldName}
                </label>
            `;
            fieldsList.appendChild(checkboxDiv);
        });
    } else {
        fieldsSection.style.display = 'none';
    }
});

function resetForm() {
    document.getElementById('customReportForm').reset();
    document.getElementById('fieldsSection').style.display = 'none';
}

// Set default date range (last 30 days)
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

    document.getElementById('date_to').value = today.toISOString().split('T')[0];
    document.getElementById('date_from').value = thirtyDaysAgo.toISOString().split('T')[0];
});
</script>
<?= $this->endSection() ?>