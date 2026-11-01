---
title: "Migrationen auf gehosteten Sites"
audience: hosting, dba, admin
category: migrations
order: 220
summary: Was ein Migrationslauf eines Kunden auf Ihren Servern tut - die Sperre der Installation, die Sicherungspunkte (Datenbank-Dumps und Dateikopien) und ihr Plattenplatz, der Wartungsmodus, Läufe im Hintergrund aus der Administration, die Engines, Git-Pushes mit den Zugangsdaten des Systembenutzers, Protokolle, Audit-Ereignisse und Überwachung - und wie er sich vom Umzug einer Site zwischen Hostern unterscheidet.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Auslieferung, Hosting, Datenbankadministrator, Sicherungspunkte, Backup, Plattenplatz, Überwachung, Cronjob, migration, migrations, schema change, database change, deploy, exp:migration:migrate, hosting, DBA, captions, disk space, proc_open, PhpCliPath, migrationpush, monitoring, fail-on-pending, migration.log, audit
type: help
---
# Migrationen auf gehosteten Sites

Diese Seite ist für Hosting-Anbieter und Datenbankadministratoren, die Sites mit Exponential 6.0.15 für Kunden
betreiben. Die Entwickler der Sites liefern Änderungen als Migrationen aus: Dateien, die die Site mit
`./console exp:migration:migrate --apply` oder aus Setup > Migrationen anwendet. Hier steht, was ein solcher Lauf auf
Ihren Servern tut, was er von ihnen braucht und worauf Sie achten sollten.

## Was ein Lauf tut

| Schritt | Auf dem Server |
|---|---|
| Die Sperre | die Datei `var/<site>/upgrade.lock` (geteilt mit `exp:upgrade`) und daneben eine Halter-Datei; je Installation schreibt nur ein Lauf gleichzeitig |
| Code-Prüfung | Git wird gefragt, ob `settings/`, `extension/` oder `design/` nicht committete Änderungen haben; dann wird ein Lauf abgelehnt |
| Sicherungspunkt vorher | ein Datenbank-Dump und Kopien des `var/`-Speichers und von `settings/override` (siehe unten), wiederverwendet, solange sich nichts geändert hat |
| Migrationen | PHP-Code der Site, ausgeführt als `[RunSettings] RunAsUser` (`admin`) auf der Konsole oder als angemeldeter Benutzer in der Administration |
| Wartungsmodus | ein- und ausgeschaltet um jede Migration, deren Kopf `maintenance: yes` sagt |
| Nach einem Fehler | ein Sicherungspunkt des gescheiterten Zustands, dann die Wiederherstellung des Sicherungspunkts von vorher |
| Sicherungspunkt nachher | ein weiterer Sicherungspunkt nach einem erfolgreichen Lauf |
| Commit, Tag, Push | Statusdatei und Laufprotokoll werden im Git-Checkout der Site committet, getaggt und nach `origin` gepusht |

## Plattenplatz: die Sicherungspunkte

Sicherungspunkte sind die Backups von Läufen und Upgrades. Mit git_manager sind es die vollständigen Sicherungspunkte
von git_manager in seinem Backup-Ordner; ohne ihn der Dump von `exp:upgrade` in `var/backup/upgrade/` (oder
`migration.ini [CaptionSettings] BackupDirectory`), ohne Speicher. Ein Lauf nimmt bis zu drei (vorher, nachher und
nach einem Fehler der des Fehlers). Die Wiederverwendung (`ReuseUnchanged=enabled`) verhindert bei einer ruhigen Site
einen neuen je Lauf, aber eine Site, die oft läuft, lässt den Ordner trotzdem wachsen.

- Planen Sie den Platz wie ein Datenbank-Backup der Site mal die Zahl der aufbewahrten Sicherungspunkte.
- Listen Sie sie mit `./console exp:upgrade:captions` (oder `exp:migration:caption --list`). `exp:upgrade:captions
  --prune` behält, was `upgrade.ini [UpgradeSettings] CaptionKeep` (10), `CaptionKeepDays` (90) und
  `CaptionKeepUpgrades` sagen; die eigenen Sicherungspunkte von git_manager richten sich nach dessen `MaxBackups`.
- Nehmen Sie den Ordner in denselben Backup-Plan wie den Rest von `var/` auf oder schließen Sie ihn bewusst aus: er
  ist eine zweite Kopie.

## Engines

| Datenbank | Eine gescheiterte Migration wird zurückgenommen | Simulation | Sicherungspunkt und Wiederherstellung |
|---|---|---|---|
| PostgreSQL, SQLite | vollständig | ja | ja |
| MySQL/MariaDB | nur Daten; Schemaänderungen werden sofort committet | nein | ja |
| MongoDB | gar nicht | nein | ja |
| Oracle (ezoracle) | nur Daten | nein | ein Sicherungspunkt braucht einen eigenen Export; die Wiederherstellung geschieht von Hand, ihre Schritte werden ausgegeben |

Auf MySQL/MariaDB, Oracle und MongoDB nimmt erst die Wiederherstellung des Sicherungspunkts einen gescheiterten Lauf
wirklich zurück. Sorgen Sie dafür, dass die Dump-Werkzeuge der Engine (`mysqldump`, `pg_dump`, `mongodump`) für den
Benutzer der PHP-Kommandozeile installiert sind.

## Läufe im Hintergrund aus der Administration

Ein Lauf, eine Fortsetzung oder eine Rücknahme, in Setup > Migrationen gestartet, läuft als eigener Konsolenprozess
unter dem angemeldeten Benutzer und geht weiter, wenn der Browser geschlossen wird. Er braucht:

- `proc_open` im PHP, das die Administration ausliefert (prüfen Sie `disable_functions`);
- ein PHP-Kommandozeilenprogramm: `migration.ini [AdminSettings] PhpCliPath` (leer: `cronjob.ini [AdminSettings]
  PhpCliPath`, sonst die üblichen Orte wie `/usr/bin/php`; nie das php-fpm-Programm);
- Schreibrechte auf `var/<site>/migration/jobs/`, wo jeder Lauf seine Fortschrittsdatei und Ausgabe ablegt
  (`JobHistory`, Voreinstellung 50 Läufe).

Fehlen sie, sagt die Seite das, und der Lauf muss auf der Konsole gestartet werden. Der Prozess läuft unter dem
PHP-FPM-Benutzer der Site, wie jeder andere Schreibzugriff auf `var/`.

## Git: Zugangsdaten und Pushes

Die Aufzeichnung wird mit den Zugangsdaten gepusht, die der Systembenutzer für das Remote hat (ein SSH-Schlüssel, ein
Credential Helper); die Migrationen speichern nichts davon. Ein gescheiterter Push lässt den Lauf nicht scheitern: der
Commit bleibt lokal, der Status warnt `1 run(s) wait for their push`, und der Cronjob-Teil `migrationpush` pusht ihn
später. Nehmen Sie diesen Teil in eine Cronjob-Gruppe auf, die die Site ausführt, oder planen Sie ihn einzeln:

```bash
php runcronjobs.php migrationpush
```

## Protokolle, Audit und Überwachung

| Wo | Was |
|---|---|
| `var/log/migration.log` | jede Zeile jedes Laufs, von Geheimnissen bereinigt |
| `migrations/runs/<run-id>.log` | dasselbe je Lauf, in Git committet |
| das Audit-Protokoll | `system.migration.run`, `.apply`, `.fail`, `.rollback`, `.caption`, `.push` (`settings/audit.ini`) |

Für die Überwachung fragen Sie jede Installation nach einer Auslieferung:

```bash
./console exp:migration:status --fail-on-pending --format=json > var/log/migration-status.json
echo $?    # 0 nichts ausstehend, 1 ausstehend oder gescheitert, 3 Datenbank nicht erreichbar
```

Das JSON (`exp-migration/1`) enthält `summary`, `lock` (wer sie hält), `pushes` (Läufe, die auf ihren Push warten) und
`orphans`. Alarmieren Sie bei Exit 1 nach einem Auslieferungsfenster, bei Pushes, die länger als einen Tag warten,
und bei einer Sperre, die seit Stunden gehalten wird.

## Worauf Sie achten sollten

- Ein Lauf, der die Sperre lange hält: `exp:migration:status` nennt den Halter; eine Sperre eines beendeten Prozesses
  übernimmt der nächste Lauf, nichts muss gelöscht werden.
- Ein gescheiterter Lauf: die Site wurde automatisch wiederhergestellt; der Entwickler korrigiert die Migration und
  setzt den Lauf fort.
- Velocity oder PHP-FPM sehen eine Änderung nicht, die eine Migration an Einstellungen oder Klassen gemacht hat:
  ausliefern wie bei jeder Codeänderung.
- Platte: der Ordner der Sicherungspunkte.

## Migrationen sind kein Umzug einer Site

Eine Migration ändert eine Installation an Ort und Stelle. Die Site eines Kunden auf Ihre Server, zwischen Hostern
oder auf eine andere Datenbank-Engine umzuziehen, ist eine andere Aufgabe mit eigenen Seiten:
[Migrationen oder Umzug einer Site](../../migrationen/migrationen-oder-umzug-einer-site.md) und
[Kunden migrieren: Überblick](../migrationen/README.md).

## Wie es weitergeht

- [Migrationen für Administratoren](../../migrationen/migrationen-fuer-administratoren.md)
- [Mit Migrationen ausliefern](../../migrationen/rezepte/mit-migrationen-ausliefern.md)
- [Hinweise zu den Datenbanken](../../migrationen/rezepte/hinweise-zu-den-datenbanken.md) und
  [Sicherungspunkte und Rücknahme in der Praxis](../../migrationen/rezepte/sicherungspunkte-und-ruecknahme-in-der-praxis.md)
- Jetzt beginnen: [das Tutorial](../../../anleitungen/migrationen-tutorial.md)

## Verwandte Seiten

- [Migrationen: der Einstieg](../../migrationen/README.md)
- [Migrationen: die Anleitung](../../../anleitungen/migrationen.md)
- [Migrationen: das Tutorial](../../../anleitungen/migrationen-tutorial.md)
- [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [Migrationen in der Administration](../../migrationen/in-der-administration.md)
- [Die Migrationsbefehle](../../migrationen/befehle.md)
- [Die Migrationseinstellungen](../../migrationen/einstellungen.md)
- [Das Migrationsdateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../../migrationen/wenn-eine-migration-scheitert.md)
- [Fehlerbehebung bei Migrationen](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../../migrationen/migrationen-oder-umzug-einer-site.md)
- [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md)
- [Datenbankadministratoren: hier beginnen](../../datenbankadministratoren/datenbankadministratoren-hier-beginnen.md)
- [Änderungen ausrollen](aenderungen-ausrollen.md), [Datenbank-Backups je Engine](datenbank-backups-je-engine.md),
  [Wartungsmodus](wartungsmodus.md), [Cronjobs](cronjobs.md)
- [Der Cronjob-Teil migrationpush](../../../beispiele/cronjobs/migrationpush.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
