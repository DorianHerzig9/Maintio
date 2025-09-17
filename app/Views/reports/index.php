<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-bar-chart me-2"></i>Berichte & Analysen</h2>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i>Berichte exportieren
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= base_url('reports/export-work-orders?format=csv') ?>">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Arbeitsaufträge (CSV)
                    </a></li>
                    <li><a class="dropdown-item" href="<?= base_url('reports/export-assets?format=csv') ?>">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Anlagen (CSV)
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= base_url('reports/export-work-orders?format=pdf') ?>">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Arbeitsaufträge PDF
                    </a></li>
                    <li><a class="dropdown-item" href="<?= base_url('reports/export-assets?format=pdf') ?>">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Anlagen PDF
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Overview Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['total_work_orders'] ?></div>
                        <div class="small">Gesamt Arbeitsaufträge</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clipboard-check display-6"></i>
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
                        <div class="h5 mb-0"><?= $stats['open_work_orders'] ?></div>
                        <div class="small">Offene Aufträge</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-exclamation-triangle display-6"></i>
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
                        <div class="h5 mb-0"><?= $stats['total_assets'] ?></div>
                        <div class="small">Gesamt Anlagen</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-cpu display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h5 mb-0"><?= $stats['critical_assets'] ?></div>
                        <div class="small">Kritische Anlagen</div>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-exclamation-diamond display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Categories -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-3">Verfügbare Berichte</h4>
    </div>
</div>

<div class="row">
    <!-- Work Orders Report -->
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded p-2 me-3">
                        <i class="bi bi-clipboard-data text-white"></i>
                    </div>
                    <h5 class="card-title mb-0">Arbeitsaufträge</h5>
                </div>
                <p class="card-text">Detaillierte Berichte über alle Arbeitsaufträge, Status, Techniker-Zuweisungen und Abschlusszeiten.</p>
                <ul class="list-unstyled small text-muted mb-3">
                    <li><i class="bi bi-check-circle me-2"></i>Status-Übersicht</li>
                    <li><i class="bi bi-check-circle me-2"></i>Techniker-Performance</li>
                    <li><i class="bi bi-check-circle me-2"></i>Zeitraum-Filter</li>
                    <li><i class="bi bi-check-circle me-2"></i>Export-Funktionen</li>
                </ul>
                <a href="<?= base_url('reports/work-orders') ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-right me-1"></i>Bericht anzeigen
                </a>
            </div>
        </div>
    </div>

    <!-- Assets Report -->
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success rounded p-2 me-3">
                        <i class="bi bi-cpu-fill text-white"></i>
                    </div>
                    <h5 class="card-title mb-0">Anlagen</h5>
                </div>
                <p class="card-text">Umfassende Anlagenberichte mit Status, Wartungshistorie und Leistungskennzahlen.</p>
                <ul class="list-unstyled small text-muted mb-3">
                    <li><i class="bi bi-check-circle me-2"></i>Anlagen-Status</li>
                    <li><i class="bi bi-check-circle me-2"></i>Wartungshistorie</li>
                    <li><i class="bi bi-check-circle me-2"></i>Standort-Filter</li>
                    <li><i class="bi bi-check-circle me-2"></i>Typ-Kategorien</li>
                </ul>
                <a href="<?= base_url('reports/assets') ?>" class="btn btn-success">
                    <i class="bi bi-arrow-right me-1"></i>Bericht anzeigen
                </a>
            </div>
        </div>
    </div>

    <!-- Maintenance Report -->
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning rounded p-2 me-3">
                        <i class="bi bi-tools text-white"></i>
                    </div>
                    <h5 class="card-title mb-0">Wartung</h5>
                </div>
                <p class="card-text">Wartungsberichte mit Compliance-Tracking, Überfälligkeiten und Planungsempfehlungen.</p>
                <ul class="list-unstyled small text-muted mb-3">
                    <li><i class="bi bi-check-circle me-2"></i>Compliance-Rate</li>
                    <li><i class="bi bi-check-circle me-2"></i>Überfällige Wartungen</li>
                    <li><i class="bi bi-check-circle me-2"></i>Planungsvorschau</li>
                    <li><i class="bi bi-check-circle me-2"></i>Kostenanalyse</li>
                </ul>
                <a href="<?= base_url('reports/maintenance') ?>" class="btn btn-warning">
                    <i class="bi bi-arrow-right me-1"></i>Bericht anzeigen
                </a>
            </div>
        </div>
    </div>

    <!-- Performance Report -->
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info rounded p-2 me-3">
                        <i class="bi bi-graph-up text-white"></i>
                    </div>
                    <h5 class="card-title mb-0">Leistung</h5>
                </div>
                <p class="card-text">Performance-Analysen für Techniker, Anlagen und Wartungsprozesse mit KPI-Tracking.</p>
                <ul class="list-unstyled small text-muted mb-3">
                    <li><i class="bi bi-check-circle me-2"></i>KPI-Dashboard</li>
                    <li><i class="bi bi-check-circle me-2"></i>Trend-Analysen</li>
                    <li><i class="bi bi-check-circle me-2"></i>Vergleichsdaten</li>
                    <li><i class="bi bi-check-circle me-2"></i>Empfehlungen</li>
                </ul>
                <a href="<?= base_url('reports/performance') ?>" class="btn btn-info">
                    <i class="bi bi-arrow-right me-1"></i>Bericht anzeigen
                </a>
            </div>
        </div>
    </div>

    <!-- Custom Reports -->
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-secondary rounded p-2 me-3">
                        <i class="bi bi-gear-fill text-white"></i>
                    </div>
                    <h5 class="card-title mb-0">Benutzerdefiniert</h5>
                </div>
                <p class="card-text">Erstellen Sie individuelle Berichte mit benutzerdefinierten Filtern und Parametern.</p>
                <ul class="list-unstyled small text-muted mb-3">
                    <li><i class="bi bi-check-circle me-2"></i>Flexible Filter</li>
                    <li><i class="bi bi-check-circle me-2"></i>Datenauswahl</li>
                    <li><i class="bi bi-check-circle me-2"></i>Vorlagen speichern</li>
                    <li><i class="bi bi-check-circle me-2"></i>Zeitplanung</li>
                </ul>
                <button class="btn btn-secondary" onclick="alert('Benutzerdefinierte Berichte in Entwicklung')">
                    <i class="bi bi-arrow-right me-1"></i>Bericht erstellen
                </button>
            </div>
        </div>
    </div>

    <!-- Scheduled Reports -->
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-dark rounded p-2 me-3">
                        <i class="bi bi-clock-fill text-white"></i>
                    </div>
                    <h5 class="card-title mb-0">Geplante Berichte</h5>
                </div>
                <p class="card-text">Automatische Berichtserstellung und Verteilung nach Zeitplan per E-Mail oder System.</p>
                <ul class="list-unstyled small text-muted mb-3">
                    <li><i class="bi bi-check-circle me-2"></i>Automatisierung</li>
                    <li><i class="bi bi-check-circle me-2"></i>E-Mail-Versand</li>
                    <li><i class="bi bi-check-circle me-2"></i>Zeitplan-Verwaltung</li>
                    <li><i class="bi bi-check-circle me-2"></i>Empfänger-Listen</li>
                </ul>
                <button class="btn btn-dark" onclick="alert('Geplante Berichte in Entwicklung')">
                    <i class="bi bi-arrow-right me-1"></i>Verwalten
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Recent Reports -->
<?php if (!empty($recentReports)): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Kürzlich erstelte Berichte
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Berichtsname</th>
                                <th>Typ</th>
                                <th>Erstellt am</th>
                                <th>Erstellt von</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentReports as $report): ?>
                            <tr>
                                <td><?= esc($report['name']) ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= ucfirst($report['type']) ?>
                                    </span>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($report['created_at'])) ?></td>
                                <td><?= esc($report['created_by']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="alert('Download-Funktion in Entwicklung')">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="alert('Ansicht-Funktion in Entwicklung')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Help Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h6><i class="bi bi-question-circle me-2"></i>Hilfe zu Berichten</h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="small">Berichtstypen:</h6>
                        <ul class="small text-muted mb-0">
                            <li><strong>Arbeitsaufträge:</strong> Status, Zuweisungen, Zeiten</li>
                            <li><strong>Anlagen:</strong> Zustand, Wartung, Performance</li>
                            <li><strong>Wartung:</strong> Compliance, Planung, Kosten</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="small">Export-Formate:</h6>
                        <ul class="small text-muted mb-0">
                            <li><strong>CSV:</strong> Für Excel und Datenanalyse</li>
                            <li><strong>PDF:</strong> Für Präsentationen (in Entwicklung)</li>
                            <li><strong>Excel:</strong> Native Excel-Formate (in Entwicklung)</li>
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
    // Add any report-specific JavaScript here
    console.log('Reports dashboard loaded');

    // You could add chart libraries here for data visualization
    // Example: Chart.js, D3.js, etc.
});
</script>
<?= $this->endSection() ?>