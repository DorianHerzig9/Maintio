<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Berichte</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('reports/custom') ?>">Benutzerdefiniert</a></li>
                        <li class="breadcrumb-item active">Ergebnis</li>
                    </ol>
                </nav>
            </div>
            <div>
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i>Exportieren
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('reports/export-custom?' . http_build_query([
                            'format' => 'csv',
                            'report_type' => $reportType,
                            'fields' => implode(',', $selectedFields),
                            'filters' => $filters,
                            'report_name' => $reportName
                        ])) ?>">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV
                        </a></li>
                        <li><a class="dropdown-item" href="<?= base_url('reports/export-custom?' . http_build_query([
                            'format' => 'pdf',
                            'report_type' => $reportType,
                            'fields' => implode(',', $selectedFields),
                            'filters' => $filters,
                            'report_name' => $reportName
                        ])) ?>">
                            <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                        </a></li>
                    </ul>
                </div>
                <a href="<?= base_url('reports/custom') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Neuen Bericht erstellen
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Report Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i><?= esc($reportName) ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <strong>Berichtstyp:</strong> <?= ucfirst($reportType) ?><br>
                            <strong>Erstellt am:</strong> <?= date('d.m.Y H:i') ?><br>
                            <strong>Anzahl Datensätze:</strong> <?= count($reportData) ?>
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">
                            <strong>Filter angewendet:</strong>
                            <?php
                            $appliedFilters = [];
                            foreach ($filters as $key => $value) {
                                if (!empty($value)) {
                                    $appliedFilters[] = ucfirst($key) . ': ' . $value;
                                }
                            }
                            echo !empty($appliedFilters) ? implode(', ', $appliedFilters) : 'Keine Filter';
                            ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Data -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>Berichtsdaten
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($reportData)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="reportTable">
                            <thead>
                                <tr>
                                    <?php
                                    // Display headers based on selected fields or all available
                                    $firstRow = $reportData[0];
                                    if (!empty($selectedFields)) {
                                        foreach ($selectedFields as $field) {
                                            if (isset($firstRow[$field])) {
                                                echo '<th>' . ucfirst(str_replace('_', ' ', $field)) . '</th>';
                                            }
                                        }
                                    } else {
                                        foreach (array_keys($firstRow) as $column) {
                                            echo '<th>' . ucfirst(str_replace('_', ' ', $column)) . '</th>';
                                        }
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reportData as $row): ?>
                                <tr>
                                    <?php
                                    if (!empty($selectedFields)) {
                                        foreach ($selectedFields as $field) {
                                            if (isset($row[$field])) {
                                                $value = $row[$field];

                                                // Format specific fields
                                                if (in_array($field, ['created_at', 'updated_at', 'scheduled_date', 'completed_at', 'next_due', 'last_completed'])) {
                                                    $value = $value ? date('d.m.Y H:i', strtotime($value)) : '-';
                                                } elseif ($field === 'status') {
                                                    $value = '<span class="badge bg-' . getStatusColor($value) . '">' . getStatusText($value) . '</span>';
                                                } elseif ($field === 'priority') {
                                                    $value = '<span class="badge bg-' . getPriorityColor($value) . '">' . getPriorityText($value) . '</span>';
                                                } elseif (is_null($value)) {
                                                    $value = '-';
                                                }

                                                echo '<td>' . $value . '</td>';
                                            }
                                        }
                                    } else {
                                        foreach ($row as $value) {
                                            if (is_null($value)) {
                                                $value = '-';
                                            }
                                            echo '<td>' . esc($value) . '</td>';
                                        }
                                    }
                                    ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">Keine Daten gefunden</h5>
                        <p class="text-muted">Mit den angegebenen Filtern wurden keine Daten gefunden.</p>
                        <a href="<?= base_url('reports/custom') ?>" class="btn btn-primary">
                            <i class="bi bi-arrow-left me-1"></i>Filter anpassen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable if available
    if (typeof DataTable !== 'undefined' && document.getElementById('reportTable')) {
        new DataTable('#reportTable', {
            responsive: true,
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/de-DE.json'
            }
        });
    }
});

// Helper functions for formatting
function getStatusColor(status) {
    switch(status) {
        case 'open': return 'primary';
        case 'in_progress': return 'warning';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'open': return 'Offen';
        case 'in_progress': return 'In Bearbeitung';
        case 'completed': return 'Abgeschlossen';
        case 'cancelled': return 'Storniert';
        default: return status;
    }
}

function getPriorityColor(priority) {
    switch(priority) {
        case 'low': return 'success';
        case 'medium': return 'warning';
        case 'high': return 'danger';
        case 'urgent': return 'dark';
        default: return 'secondary';
    }
}

function getPriorityText(priority) {
    switch(priority) {
        case 'low': return 'Niedrig';
        case 'medium': return 'Mittel';
        case 'high': return 'Hoch';
        case 'urgent': return 'Dringend';
        default: return priority;
    }
}
</script>
<?= $this->endSection() ?>

<?php
function getStatusColor($status) {
    switch($status) {
        case 'open': return 'primary';
        case 'in_progress': return 'warning';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function getStatusText($status) {
    switch($status) {
        case 'open': return 'Offen';
        case 'in_progress': return 'In Bearbeitung';
        case 'completed': return 'Abgeschlossen';
        case 'cancelled': return 'Storniert';
        default: return $status;
    }
}

function getPriorityColor($priority) {
    switch($priority) {
        case 'low': return 'success';
        case 'medium': return 'warning';
        case 'high': return 'danger';
        case 'urgent': return 'dark';
        default: return 'secondary';
    }
}

function getPriorityText($priority) {
    switch($priority) {
        case 'low': return 'Niedrig';
        case 'medium': return 'Mittel';
        case 'high': return 'Hoch';
        case 'urgent': return 'Dringend';
        default: return $priority;
    }
}
?>