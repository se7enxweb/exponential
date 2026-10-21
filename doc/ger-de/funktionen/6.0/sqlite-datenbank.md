# Unterstützung für die Datenbank SQLite

Diese Seite ist für alle, die Exponential ausprobieren, auf einem Laptop entwickeln, automatisierte Tests ausführen oder
eine kleine Website auf einem Tarif ohne MySQL betreiben möchten. Mit SQLite läuft eine vollständige Exponential-Website
aus einer Datei auf der Platte, ohne einen Datenbankserver, der installiert, konfiguriert oder abgesichert werden muss.

Die Unterstützung für SQLite kam im Januar 2024 (Releases 6.0.1 bis 6.0.3, siehe die
[Chronik Januar 2024](../../geschichte/2024/2024-01a.md)) und wurde seitdem verfeinert. Alles unten beschreibt den Code, wie
er jetzt ist; ein Hinweis sagt, wo eine Einstellung in einem späteren Release als der erste Treiber hinzukam.

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit mit zusätzlichem
> Aufwand bekanntermaßen möglich. Einzelheiten: [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md).

## Was Sie bekommen

- Einen Datenbanktreiber im Kernel, `eZSQLite3DB` (`lib/ezdb/classes/ezsqlite3db.php`), ausgewählt mit dem
  Implementierungsalias `sqlite3`.
- Einen Schema-Handler, `eZSQLiteSchema` (`lib/ezdbschema/classes/ezsqliteschema.php`), sodass der
  Einrichtungsassistent, der Paketinstaller und Schemawerkzeuge wie `bin/php/ezsqldumpschema.php` und
  `bin/php/ezsqlinsertschema.php` Tabellen anlegen und lesen können.
- SQL-Dateien im Stil von SQLite in `kernel/sql/sqlite/` (`schema.sql`, `cleandata.sql`), die der Einrichtungsassistent
  für eine saubere Installation verwendet.
- Einen Einrichtungsassistenten, der SQLite als Datenbank zur Wahl stellt, prüft, ob die PHP-Erweiterung `sqlite3`
  geladen ist, und erklärt, was zu tun ist, wenn nicht.

## In fünf Minuten ausprobieren

1. Prüfen Sie, ob PHP die Erweiterung hat:

   ```bash
   php -m | grep -i sqlite3
   ```

   Es muss `sqlite3` ausgeben. Gibt es nichts aus, installieren Sie das Paket `php-sqlite3` Ihrer Distribution (oder
   schalten Sie `extension=sqlite3` in `php.ini` ein) und laden Sie PHP-FPM neu.

2. Öffnen Sie den Einrichtungsassistenten im Browser (`/setup`). Wählen Sie auf der Datenbankseite **SQLite**. Der
   Assistent wählt es vor, wenn die PHP-Erweiterung `sqlite3` vorhanden ist (Einstellung
   `[DatabaseSettings] DefaultType=sqlite3` in `settings/setup.ini`, siehe unten). Fehlt die Erweiterung, sagt der
   Assistent das und greift auf das erste verfügbare Datenbanksystem zurück.

3. Geben Sie der Datenbank einen Namen (der Assistent schlägt `sqlite.db` vor, Konstante `SQLITE_DEFAULT_FILE_NAME`). Ein
   Name ohne Pfad wird als Datei in `var/storage/sqlite3/` angelegt:

   ```bash
   ls -l var/storage/sqlite3/
   ```

4. Schließen Sie den Assistenten wie gewohnt ab. Die Website läuft jetzt aus dieser einen Datei.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich | Was er tut |
|---|---|---|---|---|---|
| `settings/site.ini` | `[DatabaseSettings]` | `DatabaseImplementation` | `ezmysqli` | Siteaccess | Auf `sqlite3` setzen, um den Treiber zu verwenden. |
| `settings/site.ini` | `[DatabaseSettings]` | `Database` | (websitespezifisch) | Siteaccess | Die Datenbankdatei. Ein Name ohne Schrägstrich liegt in `var/storage/sqlite3/`; ein absoluter Pfad (beginnend mit `/`) wird so verwendet, wie er ist; `:memory:` ist eine Wegwerf-Datenbank im Speicher. |
| `settings/site.ini` | `[DatabaseSettings]` | `ImplementationAlias[sqlite3]` | `eZSQLite3DB` | global | Bildet den Alias auf die Treiberklasse ab. |
| `settings/dbschema.ini` | `[SchemaSettings]` | `SchemaHandlerClasses[sqlite3]` | `eZSQLiteSchema` | global | Schema-Handler, den Installer und Schemawerkzeuge verwenden. |
| `settings/setup.ini` | `[DatabaseSettings]` | `DefaultType` | `sqlite3` | global | Datenbanksystem, das der Assistent zuerst aufführt. Eines von `sqlite3`, `mysqli`, `pgsql`, `mongodb`. |
| `settings/site.ini` | `[DatabaseSettings]` | `SQLitePragmas[]` | leere Liste | Siteaccess | Zusätzliche PRAGMAs `name=value`, angewendet bei jeder Verbindung (spätere Releases). |
| `settings/site.ini` | `[DatabaseSettings]` | `SQLiteTransactionWait` | `25` | Siteaccess | Sekunden, die eine Transaktion zu ihrem Beginn auf die anderen Schreiber wartet, bevor sie mit „database is busy“ aufgibt (spätere Releases). Halten Sie den Wert unter dem Zeitlimit der Anfrage: Velocity beendet eine Anfrage nach `Q.webserver.requestTimeout`, 30 s, sofern nicht geändert, daher 25; unter PHP-FPM ist die Grenze `request_terminate_timeout` des Pools. Erhöhen Sie beide zusammen. |

Die eingebauten Standards der PRAGMAs des Treibers (ebenfalls spätere Releases) sind `synchronous=NORMAL`,
`cache_size=-65536` (64 MB), `mmap_size=268435456`, `temp_store=MEMORY` und `busy_timeout=5000`, und das Journal läuft im
WAL-Modus. Siehe [die Spezifikation des Treibers](../../spezifikationen/6.0/sqlite3-datenbanktreiber.md).

## Eine Datenbankdatei verwenden, die eine andere Anwendung teilt

Seit April 2026 (`cefe8f13a2`) nimmt der Treiber in `Database` einen absoluten Pfad an und registriert die Klasse
`eZSQLite3DB` im Autoload-Array des Kernels. Damit kann eine Legacy Bridge Exponential auf genau die Datei richten, die
eine Symfony/Doctrine-Anwendung verwendet, sodass beide Stacks dieselben Daten sehen:

```ini
# settings/siteaccess/<name>/site.ini.append.php
[DatabaseSettings]
DatabaseImplementation=sqlite3
Database=/absolute/path/to/shared.db
```

Das Verzeichnis einer neuen Datenbank wird rekursiv angelegt (`ae5feaf6a7`), sodass ein verschachtelter Pfad bei der
ersten Verwendung funktioniert.

## Grenzen und gute Praxis

- SQLite erlaubt einen Schreiber zur Zeit. Viele Redakteure, die im selben Moment veröffentlichen, stellen sich
  hintereinander an (siehe `SQLiteTransactionWait`); für ein stark beschäftigtes Redaktionsteam wählen Sie MySQL,
  PostgreSQL oder MongoDB.
- Legen Sie die Datenbankdatei nicht in ein Verzeichnis, das der Webserver direkt ausliefert. In der mitgelieferten
  `.htaccess` wird nur eine kurze Liste von Pfaden unter `var/` (öffentliche Bilder, öffentliche Caches, Vorschaubilder
  von Paketen) als Dateien ausgeliefert; der Rest geht an `index.php`. Prüfen Sie auf Ihrem eigenen Host, dass die
  Datenbankdatei nicht herunterladbar ist: `curl -sI https://www.example.com/var/storage/sqlite3/sqlite.db`. Ein anderer
  Status als 200 ist das, was Sie wollen.
- Führen Sie nach einem großen Import einmal `ANALYZE` aus, damit der Abfrageplaner Statistiken hat.
- Sichern Sie mit `sqlite3 <file> ".backup <target>"`, nicht durch Kopieren der Datei, während die Website beschäftigt
  ist.

## Verwandte Seiten

- Spezifikationen: [Datenbanktreiber SQLite3](../../spezifikationen/6.0/sqlite3-datenbanktreiber.md), [Datenbanktreiber und Installer, 16. bis 30. September 2026](../../spezifikationen/6.0/datenbanktreiber-2026-09.md), [Verhalten der Treiber für SQLite und Oracle (Oktober 2026)](../../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md), [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md)
- [SQLite: Transaktionen stellen sich für die Schreibsperre an](../../bc/6.0/sqlite-transaktionen.md)
- [Installation mit einem Befehl](installation-mit-einem-befehl.md), [SQLite für Exponential Platform](plattform-sqlite-installation.md), [MongoDB-Datenbankunterstützung](mongodb-datenbankunterstuetzung.md)
- [Unterstützung für PHP 8](../../bc/6.0/php8.md) (die Korrekturen zu countable und `is_countable`, die aus den Testläufen mit SQLite kamen)
- Changelogs: [6.0.1](../../../changelogs/6.0/6.0.1.md) (der Treiber), [6.0.13](../../../changelogs/6.0/6.0.13.md) (absolute Pfade, Autoload), [6.0.14](../../../changelogs/6.0/6.0.14.md) (Lesen des Schemas)
- Chronik: [Januar 2024, erste Hälfte](../../geschichte/2024/2024-01a.md), [Januar 2024, zweite Hälfte](../../geschichte/2024/2024-01b.md), [April 2026](../../geschichte/2026/2026-04.md), [Juni 2026, zweite Hälfte](../../geschichte/2026/2026-06b.md) (Entfernen von Indizes)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
