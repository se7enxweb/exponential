# Migrationen im Kernel

Exponential 6.0.15 bringt Migrationen in den Kernel: Änderungen an einer Installation (eine Tabelle, Inhalte, eine
Klasse, eine Rolle, eine Einstellung), einmal in Dateien unter Versionskontrolle geschrieben und auf jede Installation
einer Site in derselben Reihenfolge angewendet, mit einer Aufzeichnung, was wo gelaufen ist, einem Sicherungspunkt vor
jedem Lauf, einer automatischen Wiederherstellung nach einem Fehler und einem Commit, Tag und Push der Aufzeichnung
jedes Laufs. Vor 6.0.15 wurden solche Änderungen von Hand oder mit Update-Skripten angewendet, und nichts hielt fest,
welche gelaufen waren.

Die Anleitung dazu ist [Migrationen](../../anleitungen/migrationen.md); beginnen Sie mit
[dem Tutorial](../../anleitungen/migrationen-tutorial.md).

Schnellstart:

```bash
./console exp:migration:generate --skeleton add_news_class --apply
./console exp:migration:check
./console exp:migration:migrate              # Probelauf
./console exp:migration:migrate --apply
./console exp:migration:status
```

## Was 6.0.15 hinzufügt

| Teil | Was es ist |
|---|---|
| Die Upgrade-Schritte des Kernels | jeder Schritt von `exp:upgrade` (die des Kernels und die der Erweiterungen) ist eine Migration der Quelle `kernel`: vom Status und der Administration zuerst gelistet, von einem Lauf zuerst ausgeführt (Stufen `database` und `scripts`) und in `requires` nennbar |
| Migrationsdateien | `YYYYMMDDHHMMSS_name.php` in `migrations/` der Site, in `extension/<name>/migrations/` aktiver Erweiterungen und in installierten Paketen; ein Kopf aus Kommentarzeilen `schlüssel: wert` (description, requires, environments, engines, maintenance, author); geordnet nach ID und `requires` |
| PHP-Migrationen | eine Klasse in einem Namensraum, die `expMigrationPhp` erweitert, mit `plan()`, `up()`, wahlweise `down()` und `detect()` |
| Die Aufzeichnung | Tabellen `exp_migration` (eine Zeile je Migration; seit 6.0.15 auch die Aufzeichnung von `exp:upgrade`) und `exp_migration_run` (eine Zeile je Lauf), angelegt vom Upgrade-Schritt `6.0.15-migration-tables` |
| Der Runner | die mit `exp:upgrade` geteilte Sperre der Installation, eine Transaktion je Migration, wo die Engine eine hat, der Benutzer des Laufs, Wartungsmodus je Migration, eine Prüfsummen-Sperre gegen geänderte Dateien, `ezpEvent`-Ereignisse und Audit-Ereignisse |
| Sicherungspunkte | ein Sicherungspunkt vor jedem Lauf (wiederverwendet, solange sich nichts geändert hat), einer nach einem erfolgreichen Lauf, einer des gescheiterten Zustands und die Wiederherstellung des Sicherungspunkts von vorher nach einem Fehler; die Sicherungspunkte sind die von `exp:upgrade` |
| Zurücknehmen | ein Lauf (sein Sicherungspunkt von vorher), eine Migration (ihr `down()`) oder, für Fachleute, ein beliebiger Sicherungspunkt |
| Das Migrations-Repository | die Statusdatei `status/<umgebung>.json` und das Laufprotokoll `runs/<run-id>.log`, nach jedem Lauf committet, getaggt und gepusht; gescheiterte Pushes wiederholt der Cronjob-Teil `migrationpush` |
| Befehle | `exp:migration:status`, `:migrate`, `:dry-run` (mit `--simulate --diff`), `:rollback`, `:resume`, `:caption`, `:push`, `:check`, `:generate`; `:migrate` und `:resume` nehmen `--run-id` (die Kennung des zu startenden Laufs) und `--progress` (eine Datei, an die der Lauf seinen Fortschritt anhängt, eine JSON-Zeile je Ereignis), `:rollback` nimmt `--progress` |
| Setup > Migrationen | das Modul `migration` mit den Ansichten `status`, `history`, `log/<run-id>`, `stream/<run-id>` (der Live-Fortschritt), `dryrun`, `run` und `rollback/<target>`; die Richtlinien `migration/read`, `run`, `rollback` und `edit` (in einer neuen Installation nur Administrator); „Identität bestätigen“ vor einem Lauf, einer Fortsetzung, einer Simulation und einer Rücknahme |
| Läufe im Hintergrund | ein in der Administration gestarteter Lauf, eine Fortsetzung oder Rücknahme läuft als eigener Konsolenprozess unter dem angemeldeten Benutzer (`expMigrationJob`), geht weiter, wenn die Seite geschlossen wird, und wird über seine Fortschrittsdatei live verfolgt; seine Dateien liegen in `var/<site>/migration/jobs/` |
| Dashboard-Block | `migrations`: ausstehend, fehlgeschlagen, verwaist, der laufende und der letzte Lauf (Fetch-Funktion `migration/summary`) |
| Änderungen aufzeichnen | die eigenen Änderungen eines Benutzers (Sitzung) oder jede Änderung (websiteweites Fenster), auf der Ebene der Operationen des Kernels als Schritte einer YAML-Migration aufgezeichnet, ohne personenbezogene Daten; Tabellen `exp_migration_recording` und `exp_migration_recording_event` (Upgrade-Schritt `6.0.15-migration-recording-tables`); die Befehle `exp:migration:record:start`, `:stop`, `:status`, `:list`, `:show`, `:discard`; Setup > Migrationen > Aufzeichnung und der Aufzeichnungsbalken ([die Anleitung](../../anleitungen/migrationen-erzeugen-und-aufzeichnen.md)) |
| Einstellungen | `settings/migration.ini`: `[MigrationSettings]`, `[RunSettings]`, `[CaptionSettings]`, `[RepositorySettings]`, `[AdminSettings]` (`PhpCliPath`, `JobHistory`, `LogBytes`) |

## Die Bausteine

| Klasse (`kernel/classes/migration/`) | Aufgabe |
|---|---|
| `expMigration` | die Schnittstelle einer Migration (ID, Quelle, Zustand, plan, up, down) |
| `expMigrationDefinition` | eine Datei auf der Platte: Name, Kopf, Prüfsumme |
| `expMigrationLocator` | findet die Dateien der Site, der Erweiterungen und der Pakete |
| `expMigrationGraph` | die Reihenfolge und ihre Fehler (Doppelte, fehlende Anforderungen, Zyklen) |
| `expMigrationPhp` | die Basisklasse einer PHP-Migration |
| `expMigrationContext` | woran ein Lauf arbeitet: Datenbank, Benutzer, Umgebung, Referenzen, Aufzeichnung, Sperre, Protokoll |
| `expMigrationRunner` | führt Migrationen aus und nimmt eine mit `down()` zurück |
| `expMigrationRunFlow` | Sicherungspunkte, Wiederherstellung, Commit, Tag und Push um einen Lauf |
| `expMigrationCaption`, `expMigrationRollback`, `expMigrationResume` | Sicherungspunkte, die drei Arten des Zurücknehmens, einen Lauf fortsetzen |
| `expMigrationRecord`, `expMigrationStatusMirror`, `expMigrationRepository` | die Tabellen, die Dateien in Git, Git |
| `expMigrationLock` | die Sperre der Installation mit Halter-Datei (auch die Sperre von `exp:upgrade`) |
| `expMigrationStatus`, `expMigrationCheck`, `expMigrationSimulation`, `expMigrationGenerator` | der Status, die Prüfung, die Simulation, das Gerüst |
| `expMigrationUpgradeSource`, `expMigrationUpgradeStep` | die Upgrade-Schritte des Kernels (`exp:upgrade`) als Migrationen der Quelle `kernel` |
| `expMigrationAdmin`, `expMigrationJob`, `expMigrationProgress` | die Formulare und die Dashboard-Zusammenfassung von Setup > Migrationen, die dort im Hintergrund gestarteten Läufe, ihre Fortschrittsdatei |
| `expMigrationRecording`, `expMigrationRecorder`, `expMigrationRecordingEvent`, `expMigrationRecordingResolver`, `expMigrationRecordingGenerator` | die Aufzeichnungen und ihre Ereignisse, der Abgriff an den Audit-Stellen, ein Audit-Ereignis als Migrationsschritt, die stabilen Namen aus der Installation, der Plan einer Aufzeichnung |

Die Befehle sind `bin/php/expmigration<name>.php` (Alias `exp:migration:<name>`), ihr Code liegt in
`kernel/private/classes/commands/`. Ihre Maschinenausgabe ist `exp-migration/1` (`--format=json`); die Exit-Codes sind
die von `exp:upgrade` (0, 1 gescheitert, 2 Bedienung, 3 Datenbank nicht erreichbar, 4 intern).

## Engines

| Datenbank | Transaktionen | Simulation | Sicherungspunkt und Wiederherstellung |
|---|---|---|---|
| SQLite | vollständig | ja | ja |
| PostgreSQL | vollständig | ja | ja |
| MySQL/MariaDB | nur Daten (Schemaänderungen werden sofort committet) | nein | ja |
| MongoDB | keine | nein | ja |
| Oracle (ezoracle) | nur Daten | nein | ein Sicherungspunkt braucht einen eigenen Export; die Wiederherstellung geschieht von Hand (die Schritte werden ausgegeben) |

## Grenzen in 6.0.15

- Migrationen werden in PHP, in YAML, in SQL (`sql`, `<engine>.sql`, `mongodb.json`) oder als INI-Einstellungen (`ini`)
  geschrieben ([Migrationen: SQL und Einstellungen](../../anleitungen/migrationen-sql-und-einstellungen.md)).
- Ein Migrationslauf führt von den Upgrade-Schritten des Kernels nur die der Stufen `database` und `scripts` aus; die
  Stufen `code`, `libraries` und `finish` bleiben bei `exp:upgrade` (sie werden als `deferred` gelistet).
- Setup > Migrationen zeigt Migrationen an, führt sie aus, nimmt sie zurück, zeichnet Änderungen auf und erzeugt eine
  Migration aus einer Aufzeichnung (die Richtlinie `migration/edit`); das Schreiben von Migrationen, die anderen
  Generatoren und die Einstellungen des Migrations-Repositorys geschehen in Dateien und auf der Konsole.
- Eine Migration, die aus einer Aufzeichnung oder aus dem Vorhandenen erzeugt wird, lässt aus, was kein Schritttyp
  wiederholt (das Vertauschen zweier Orte, Workflow-Auslöser, Einstellungen von Erweiterungen, geheime Einstellungen),
  und nennt es oben in der Datei; das Erzeugen aus einem Vergleich und der Konverter für das YAML des
  Referenz-Bundles gibt es noch nicht.
- Ein in der Administration gestarteter Lauf braucht `proc_open` und ein PHP-Kommandozeilenprogramm
  (`migration.ini [AdminSettings] PhpCliPath`); fehlen sie, sagt die Seite das, und die Konsole führt ihn aus.
- Commits, Tags und Pushes brauchen git_manager; ohne ihn sagt ein Lauf das und committet nichts.

## Einstellungen

Jede Einstellung mit ihrer Voreinstellung: [migration.ini](../../beispiele/ini-einstellungen/migration/README.md). Der
Cronjob-Teil: [migrationpush](../../beispiele/cronjobs/migrationpush.md). Audit-Ereignisse: `system.migration.run`,
`.apply`, `.fail`, `.rollback`, `.caption`, `.push`, `.record` in `settings/audit.ini`.

## Verwandte Seiten

- [Migrationen: der Einstieg](../../hilfe/migrationen/README.md),
  [Alle Rezepte](../../hilfe/migrationen/rezepte/README.md), [Häufige Fragen](../../hilfe/migrationen/faq.md)
- [Die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)
- [In der Administration](../../hilfe/migrationen/in-der-administration.md),
  [Befehle](../../hilfe/migrationen/befehle.md), [Einstellungen](../../hilfe/migrationen/einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](../../hilfe/migrationen/richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../../hilfe/migrationen/wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../../hilfe/migrationen/migrationen-oder-umzug-einer-site.md)
- [Migrationen in der Administration](../../hilfe/migrationen/in-der-administration.md),
  [die Ansichten des Moduls migration](../../beispiele/modulansichten/migration/README.md),
  [seine Richtlinien](../../beispiele/richtlinien/migration.md)
- [Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md)
