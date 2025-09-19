# Business Layer Architecture

Diese Implementierung zeigt eine saubere 2-Layer Architektur für den PreventiveMaintenance Bereich.

## Architektur-Übersicht

```
Controller (Framework Layer)
    ↓
Application Service (Orchestration Layer)
    ↓
Business Services (Domain Logic Layer)
```

## Verzeichnisstruktur

```
/src/
├── Application/
│   └── PreventiveMaintenanceApplicationService.php
└── Domain/
    ├── ScheduleCalculationService.php
    ├── MaintenanceStatisticsService.php
    ├── WorkOrderGenerationService.php
    └── ScheduleValidationService.php
```

## Layer-Beschreibung

### 1. Business Controller Application Service (`/src/Application`)

**Zweck**: Orchestriert den Ablauf und nimmt Requests entgegen
- Ruft Business Services auf
- Koordiniert zwischen mehreren Services
- Konvertiert Input/Output für Framework
- **Enthält KEINE Geschäftslogik**

**Beispiel**: `PreventiveMaintenanceApplicationService`
- `createSchedule()` - Orchestriert Validierung + Speicherung
- `getMaintenanceStatistics()` - Koordiniert Datenabfrage + Berechnung
- `generateWorkOrders()` - Orchestriert Work Order Generierung

### 2. Business Services (`/src/Domain`)

**Zweck**: Enthält die reine Geschäftslogik
- Berechnungen, Regeln, Analysen
- Framework-unabhängig
- Keine DB-Abhängigkeiten
- Testbar ohne externe Abhängigkeiten

#### Services:

**`ScheduleCalculationService`**
- Berechnung von Fälligkeitsterminen
- Intervall-Logik (täglich, wöchentlich, monatlich, etc.)
- Überfälligkeits-Prüfung

**`MaintenanceStatisticsService`**
- Statistik-Berechnungen (überfällig, anstehend, etc.)
- Filterung nach Status/Priorität
- Datenaufbereitung für Dashboards

**`WorkOrderGenerationService`**
- Work Order Erstellung aus Maintenance Schedules
- Work Order Nummer Generierung
- Validierung der Generierungs-Regeln

**`ScheduleValidationService`**
- Input-Validierung
- Business Rule Validation
- Daten-Sanitisierung

## Vorteile dieser Architektur

### ✅ Saubere Trennung
- **Application Service**: Orchestrierung ohne Geschäftslogik
- **Business Services**: Reine Geschäftslogik ohne Framework-Dependencies

### ✅ Testbarkeit
- Business Services sind framework-unabhängig testbar
- Klare Interfaces zwischen den Layern
- Mocking von Dependencies möglich

### ✅ Wartbarkeit
- Geschäftslogik ist gekapselt und wiederverwendbar
- Änderungen an Framework betreffen nur Application Layer
- Business Rules sind zentral organisiert

### ✅ Erweiterbarkeit
- Neue Business Services können einfach hinzugefügt werden
- Application Services können mehrere Business Services orchestrieren
- Framework-Wechsel ist einfacher möglich

## Verwendung

### Im Controller:
```php
// Initialisierung der Services
$scheduleCalculationService = new ScheduleCalculationService();
$statisticsService = new MaintenanceStatisticsService($scheduleCalculationService);
$applicationService = new PreventiveMaintenanceApplicationService(...);

// Verwendung
$result = $applicationService->createSchedule($data);
$stats = $applicationService->getMaintenanceStatistics();
```

### Business Logic Testing:
```php
// Business Services können isoliert getestet werden
$calculationService = new ScheduleCalculationService();
$nextDue = $calculationService->calculateNextDueDate('monthly', 3);
$isOverdue = $calculationService->isOverdue($nextDue);
```

## Migration vom alten Code

Der ursprüngliche `PreventiveMaintenance` Controller wurde refaktoriert zu:
- **Controller**: `PreventiveMaintenanceRefactored` (nur Orchestrierung)
- **Application Service**: `PreventiveMaintenanceApplicationService` (Ablauf-Steuerung)
- **Business Services**: 4 spezialisierte Services (Geschäftslogik)

Alle Funktionalitäten bleiben erhalten, aber die Architektur ist jetzt sauber getrennt und testbar.