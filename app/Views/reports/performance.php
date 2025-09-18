<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Berichte</a></li>
                        <li class="breadcrumb-item active">Leistung</li>
                    </ol>
                </nav>
            </div>
            <div class="dropdown">
                <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i>Exportieren
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="alert('CSV Export in Entwicklung')">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="alert('PDF Export in Entwicklung')">
                        <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-funnel me-2"></i>Filter
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('reports/performance') ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="period" class="form-label">Zeitraum</label>
                            <select class="form-select" id="period" name="period">
                                <?php foreach ($periodOptions as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $filters['period'] === $key ? 'selected' : '' ?>>
                                        <?= esc($value) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="technician" class="form-label">Techniker</label>
                            <select class="form-select" id="technician" name="technician">
                                <option value="">Alle Techniker</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>" <?= $filters['technician'] == $tech['id'] ? 'selected' : '' ?>>
                                        <?= esc($tech['first_name'] . ' ' . $tech['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Filter anwenden
                            </button>
                            <a href="<?= base_url('reports/performance') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x me-1"></i>Zurücksetzen
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Performance Sections -->
<div class="row mb-4">
    <!-- Technician Performance -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>Techniker-Performance
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($performanceData['technician_performance'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Techniker</th>
                                    <th>Gesamt Aufträge</th>
                                    <th>Abgeschlossen</th>
                                    <th>Abschlussrate</th>
                                    <th>Ø Bearbeitungszeit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($performanceData['technician_performance'] as $tech): ?>
                                <tr>
                                    <td><?= esc($tech['first_name'] . ' ' . $tech['last_name']) ?></td>
                                    <td><?= $tech['total_orders'] ?></td>
                                    <td><?= $tech['completed_orders'] ?></td>
                                    <td>
                                        <?php
                                        $rate = $tech['total_orders'] > 0 ? round(($tech['completed_orders'] / $tech['total_orders']) * 100, 1) : 0;
                                        ?>
                                        <span class="badge bg-<?= $rate >= 80 ? 'success' : ($rate >= 60 ? 'warning' : 'danger') ?>">
                                            <?= $rate ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (isset($tech['avg_completion_days']) && $tech['avg_completion_days']): ?>
                                            <?= round($tech['avg_completion_days'], 1) ?> Tage
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Keine Techniker-Performance-Daten für den ausgewählten Zeitraum verfügbar.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Asset Performance -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cpu me-2"></i>Anlagen-Performance
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($performanceData['asset_performance'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Anlage</th>
                                    <th>Arbeitsaufträge</th>
                                    <th>Abgeschlossen</th>
                                    <th>Erfolgsrate</th>
                                    <th>Ø Reparaturzeit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($performanceData['asset_performance'] as $asset): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($asset['asset_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($asset['asset_number']) ?></small>
                                    </td>
                                    <td><?= $asset['total_work_orders'] ?></td>
                                    <td><?= $asset['completed_orders'] ?></td>
                                    <td>
                                        <?php
                                        $rate = $asset['total_work_orders'] > 0 ? round(($asset['completed_orders'] / $asset['total_work_orders']) * 100, 1) : 0;
                                        ?>
                                        <span class="badge bg-<?= $rate >= 90 ? 'success' : ($rate >= 70 ? 'warning' : 'danger') ?>">
                                            <?= $rate ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (isset($asset['avg_repair_time']) && $asset['avg_repair_time']): ?>
                                            <?= round($asset['avg_repair_time'], 1) ?> Tage
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Keine Anlagen-Performance-Daten für den ausgewählten Zeitraum verfügbar.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Maintenance Performance -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tools me-2"></i>Wartungs-Performance
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3><?= $performanceData['maintenance_performance']['compliance_rate'] ?>%</h3>
                                <p class="mb-0">Compliance Rate</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3><?= count($performanceData['maintenance_performance']['overdue_maintenance']) ?></h3>
                                <p class="mb-0">Überfällige Wartungen</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3><?= count($performanceData['maintenance_performance']['upcoming_maintenance']) ?></h3>
                                <p class="mb-0">Anstehende Wartungen</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3><?= $performanceData['maintenance_performance']['maintenance_costs']['total_costs'] ?>€</h3>
                                <p class="mb-0">Wartungskosten</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($performanceData['maintenance_performance']['overdue_maintenance'])): ?>
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-danger">Überfällige Wartungen</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Anlage</th>
                                        <th>Beschreibung</th>
                                        <th>Fällig seit</th>
                                        <th>Überfällig</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($performanceData['maintenance_performance']['overdue_maintenance'] as $maintenance): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($maintenance['asset_name']) ?></strong><br>
                                            <small class="text-muted"><?= esc($maintenance['asset_number']) ?></small>
                                        </td>
                                        <td><?= esc($maintenance['description']) ?></td>
                                        <td><?= date('d.m.Y', strtotime($maintenance['next_due_date'])) ?></td>
                                        <td>
                                            <?php
                                            $overdueDays = (time() - strtotime($maintenance['next_due_date'])) / (60 * 60 * 24);
                                            ?>
                                            <span class="badge bg-danger"><?= floor($overdueDays) ?> Tage</span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Future Features Info -->
<div class="row">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h6><i class="bi bi-lightbulb me-2"></i>Geplante Features für Performance-Berichte</h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="small">Techniker-Metriken:</h6>
                        <ul class="small text-muted mb-0">
                            <li>Durchschnittliche Bearbeitungszeit pro Auftrag</li>
                            <li>Abschlussrate und Qualitätsbewertungen</li>
                            <li>Produktivitäts- und Effizienz-Kennzahlen</li>
                            <li>Vergleichsanalysen zwischen Technikern</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="small">Anlagen-Metriken:</h6>
                        <ul class="small text-muted mb-0">
                            <li>OEE (Overall Equipment Effectiveness)</li>
                            <li>MTBF und MTTR Berechnungen</li>
                            <li>Verfügbarkeits- und Zuverlässigkeitsanalysen</li>
                            <li>Kosten-Nutzen-Analysen für Wartungsstrategien</li>
                        </ul>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="small">Visualisierungen:</h6>
                        <ul class="small text-muted mb-0">
                            <li>Interaktive Dashboards mit Charts</li>
                            <li>Trend-Analysen und Prognosen</li>
                            <li>Heatmaps für Performance-Hotspots</li>
                            <li>Drill-Down-Funktionalität</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="small">Export-Optionen:</h6>
                        <ul class="small text-muted mb-0">
                            <li>Detaillierte Excel-Berichte mit Formeln</li>
                            <li>PDF-Berichte für Management</li>
                            <li>Automatisierte Berichterstellung</li>
                            <li>E-Mail-Verteilung von KPI-Reports</li>
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
    console.log('Performance report loaded');

    // Hier könnten Chart-Bibliotheken wie Chart.js eingebunden werden
    // für die zukünftige Implementierung von Performance-Dashboards
});
</script>
<?= $this->endSection() ?>