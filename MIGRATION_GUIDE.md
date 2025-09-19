# Migration von SQLite zu MySQL - Schritt-für-Schritt Anleitung

## 🚀 Übersicht
Diese Anleitung hilft dir dabei, das Maintio-Projekt von SQLite auf MySQL umzustellen.

## ✅ Voraussetzungen
- **XAMPP gestartet** (Apache + MySQL)
- **phpMyAdmin** erreichbar über `http://localhost/phpmyadmin`

## 📋 Schritt-für-Schritt Migration

### Schritt 1: XAMPP starten
1. XAMPP Control Panel öffnen
2. **Apache** starten
3. **MySQL** starten
4. Beide sollten grün markiert sein

### Schritt 2: MySQL-Datenbank erstellen
1. Öffne `http://localhost/phpmyadmin` im Browser
2. Klicke auf **"Neu"** (links in der Sidebar)
3. Datenbankname: `maintio`
4. Zeichensatz: `utf8mb4_unicode_ci`
5. Klicke **"Anlegen"**

### Schritt 3: Tabellen mit SQL-Skript erstellen
1. Wähle die `maintio` Datenbank aus
2. Klicke auf **"SQL"** Tab
3. Öffne die Datei: `src/db-migrations/create_tables_2025-09-19.sql`
4. Kopiere den **kompletten Inhalt** der SQL-Datei
5. Füge ihn in das SQL-Textfeld ein
6. Klicke **"OK"** zum Ausführen

### Schritt 4: Überprüfung der Migration
Nach dem Ausführen solltest du sehen:
- ✅ 6 Tabellen erstellt
- ✅ Sample-Daten eingefügt
- ✅ Views und Trigger erstellt

**Tabellen die erstellt werden:**
- `users` (Benutzer)
- `assets` (Anlagen)
- `work_orders` (Arbeitsaufträge)
- `preventive_maintenance` (Instandhaltungspläne)
- `work_order_components` (Komponenten)
- `settings` (Einstellungen)

### Schritt 5: Anwendung testen
1. Starte deinen lokalen Webserver
2. Öffne die Maintio-Anwendung
3. Teste die Funktionen:
   - Login (admin/admin123)
   - Instandhaltungspläne anzeigen
   - Arbeitsaufträge erstellen

## 🔧 Konfiguration

### Datenbank-Verbindung
Die Konfiguration wurde bereits angepasst in:
`app/Config/Database.php`

**MySQL-Verbindung:**
```php
'hostname' => 'localhost',
'username' => 'root',
'password' => '',        // XAMPP Standard: leer
'database' => 'maintio',
'DBDriver' => 'MySQLi',
'port'     => 3306,
```

### Standard-Login
**Benutzername:** `admin`
**Passwort:** `admin123`

## 🚨 Problembehandlung

### Problem: Verbindung fehlgeschlagen
**Lösung:**
1. XAMPP MySQL-Service prüfen (muss grün sein)
2. Datenbank `maintio` existiert?
3. Port 3306 verfügbar?

### Problem: Tabellen nicht gefunden
**Lösung:**
1. SQL-Skript nochmals ausführen
2. Prüfe ob alle Tabellen in phpMyAdmin sichtbar sind

### Problem: Syntax-Fehler im SQL
**Lösung:**
1. SQL-Skript in kleineren Blöcken ausführen
2. Starte mit der `CREATE DATABASE` Anweisung

## 📊 Vorteile der MySQL-Migration

### ✅ Performance
- Bessere Performance bei größeren Datenmengen
- Optimierte Indizes für häufige Abfragen

### ✅ Funktionalität
- Fremdschlüssel-Constraints
- Trigger und Views
- Backup und Restore-Funktionen

### ✅ Produktionsreif
- Multi-User Support
- Bessere Concurrent Access
- Professionelle Datenbank für Produktionsumgebung

## 🔄 Rollback (falls nötig)

Falls du zurück zu SQLite möchtest:

1. Ändere in `app/Config/Database.php`:
```php
public string $defaultGroup = 'sqlite_backup';
```

2. Die SQLite-Datei bleibt erhalten unter:
`project-root/writable/database/maintio.db`

## ✅ Erfolgreich!

Nach der Migration läuft dein Maintio-System mit einer robusten MySQL-Datenbank!

**Nächste Schritte:**
1. Backup-Strategie implementieren
2. Performance-Monitoring einrichten
3. Produktive Daten migrieren (falls vorhanden)