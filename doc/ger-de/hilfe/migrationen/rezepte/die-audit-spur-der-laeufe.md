---
title: "Die Audit-Spur der Migrationsläufe"
audience: admin, developer, hosting, dba, software-engineer
category: migrations
order: 20
summary: Wo ein Migrationslauf seine Spuren hinterlässt - die Tabellen exp_migration und exp_migration_run, die Statusdatei und das Laufprotokoll in Git, var/log/migration.log und die Audit-Ereignisse system.migration.* - und wie Sie beantworten, was wo, wann und von wem gelaufen ist.
keywords: Migration, Migrationen, Audit, Audit-Spur, Audit-Protokoll, Verlauf, Statusdatei, Laufprotokoll, Aufzeichnung, migration, migrations, schema change, database change, audit trail, audit log, history, exp_migration, exp_migration_run, status file, run log, migration.log, system.migration.run, system.migration.apply, system.migration.fail, system.migration.rollback, system.migration.caption, system.migration.push
type: help
---
# Die Audit-Spur der Migrationsläufe

Eine gelaufene Migration hinterlässt an fünf Stellen eine Spur. Zusammen beantworten sie die Fragen eines Audits,
eines Supportfalls oder einer Release-Prüfung: was lief, wo, wann, durch wen, mit welcher Datei, was es geändert hat
und wie man zurückkommt. Diese Seite zeigt jede Stelle und den Befehl, der sie liest.

## Was Sie am Ende haben

- die Befehle und Dateien, die sagen, was eine Installation angewendet hat;
- die Audit-Ereignisse der Migrationen eingeschaltet und im Audit-Protokoll gefunden;
- die Aufzeichnung anderer Umgebungen aus Git gelesen, ohne Zugriff auf deren Datenbanken.

## Bevor Sie beginnen

- Die Aufzeichnungstabellen existieren: `./console exp:upgrade:status` zeigt `6.0.15-migration-tables` als `done`.
- Für den Git-Teil wird das Migrations-Repository committet und gepusht ([Tags pushen](tags-pushen.md)).

## Die fünf Stellen

| Wo | Was | Wer liest es |
|---|---|---|
| Tabelle `exp_migration` | eine Zeile je Migration (Quelle + ID): Zustand, Lauf-ID, Prüfsumme der Datei, Zeiten, Dauer, `run_by`, Engine, der Sicherungspunkt davor, der Commit danach, die letzte Meldung | `exp:migration:status`, Setup > Migrationen |
| Tabelle `exp_migration_run` | eine Zeile je Lauf: Zustand (`running`, `done`, `failed`, `rolled-back`), Benutzer, Umgebung, Sicherungspunkte davor und danach, Commit, Tag, Push-Zustand | der Verlauf, `exp:migration:status` |
| `migrations/status/<umgebung>.json` | die Aufzeichnung in Git gespiegelt, sortiert, ohne Benutzer und Meldungen | jeder mit einem Klon |
| `migrations/runs/<run-id>.log` | eine Zeile je Ereignis des Laufs | jeder mit einem Klon |
| das Audit-Protokoll | Ereignisse `system.migration.*` | die Audit-Konsole, ihre Senken und Webhooks |

Jeder Lauf schreibt seine Zeilen außerdem nach `var/log/migration.log`, mit der Lauf-ID davor.

## Schritte

### 1. Die Aufzeichnung auf der Konsole lesen

```bash
./console exp:migration:status --runs=5
```

```text
Exponential migrations: status
Run 20261101-111500-91ab2c, environment production, database postgresql (transactions: full)

  done        20261101090000_news_note_priority             site               The column priority of exp_news_note
              recorded done by run 20261101-100500-7c4d11 on 2026-11-01T10:05:00+00:00

The latest runs, newest first:
  20261101-100500-7c4d11   done        2026-11-01T10:05:00+00:00  cli www-data, pushed
Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown
PASS
```

`--format=json` liefert dasselbe als Maschinenausgabe `exp-migration/1`, mit `record` je Migration und `runs`.

### 2. Die Aufzeichnung in der Administration lesen

Setup > Migrationen > Verlauf führt jeden Lauf mit seinen Sicherungspunkten, Commit, Tag und Push; die Seite eines
Laufs zeigt seine Migrationen und das Ende seines Laufprotokolls ([Stand und Verlauf](../status-und-verlauf.md)). Die
Richtlinie `migration/read` genügt.

### 3. Das Protokoll eines Laufs lesen

```text
2026-11-01T10:05:00Z info run 20261101-100500-7c4d11 by www-data on postgresql in production: 1 to apply
2026-11-01T10:05:00Z info applied 20261101090000_news_note_priority in 41 ms: added the column exp_news_note.priority (1 statement(s))
2026-11-01T10:05:00Z info run 20261101-100500-7c4d11 done: 1 applied, 0 failed
```

Bevor eine Zeile geschrieben wird, werden geheime Werte, Zugangsdaten in URLs, `password=...`-Paare, Bearer-Token und
E-Mail-Adressen entfernt, sodass das Protokoll gepusht werden kann. Geben Sie aus `up()` trotzdem keine Geheimnisse
zurück.

### 4. Die Audit-Ereignisse einschalten und lesen

Die mitgelieferte `settings/audit.ini` schaltet sie ein:

```ini
[AuditEventSettings]
Enabled[]=system.migration.run
Enabled[]=system.migration.apply
Enabled[]=system.migration.fail
Enabled[]=system.migration.rollback
Enabled[]=system.migration.caption
Enabled[]=system.migration.push
```

| Ereignis | Festgehalten, wenn |
|---|---|
| `system.migration.run` | ein Lauf mit `--apply` endet |
| `system.migration.apply` | eine Migration angewendet oder eine geänderte Datei mit `--force --reason` akzeptiert wird |
| `system.migration.fail` | eine Migration scheitert |
| `system.migration.rollback` | ein Lauf, ein Sicherungspunkt oder eine Migration zurückgenommen wird (Verben `rollback-run`, `rollback-caption`, `rollback`) |
| `system.migration.caption` | ein Sicherungspunkt angelegt wird |
| `system.migration.push` | die Aufzeichnung eines Laufs gepusht wird |

Die Ereignisse laufen wie jedes andere durch die Senken des Audits, eine Webhook- oder Mail-Senke gibt sie also weiter.

### 5. Eine andere Umgebung aus Git lesen

```bash
git fetch --tags
git tag -l 'migrations/production/*' --sort=version:refname | tail -5
git show origin/main:migrations/status/production.json | head -20
```

Die Statusdatei sagt, was Produktion angewendet hat, der Tag nennt ihre Läufe, `runs/<run-id>.log` was jeder Lauf tat.

## Die üblichen Fragen beantworten

| Frage | Wo |
|---|---|
| Hat Produktion Migration X angewendet? | `migrations/status/production.json` oder `exp:migration:status` dort |
| Wer hat sie ausgeführt? | `exp_migration.run_by` (`cli <Systembenutzer>` oder `web <Systembenutzer>`), der Benutzer des Laufs in `exp_migration_run`, der Akteur des Audit-Ereignisses |
| Welche Datei lief? | `exp_migration.checksum`; eine spätere Änderung lehnt die Prüfsummen-Sperre ab |
| Wurde eine geänderte Datei akzeptiert? | `system.migration.apply` mit der Begründung |
| Was hat sie geändert? | das Laufprotokoll; mit `--simulate --diff` vor dem Lauf der Tabellenvergleich |
| Wie kommen wir zurück? | der `caption_pre` des Laufs; siehe [Sicherungspunkte und Rücknahme in der Praxis](sicherungspunkte-und-ruecknahme-in-der-praxis.md) |

## Prüfen

Führen Sie auf einer Entwicklungsinstallation eine Migration aus und finden Sie sie an allen fünf Stellen: die
Statuszeile, der Verlauf, das Protokoll, `system.migration.apply` in der Audit-Konsole und der Commit in Git.

## Nächster Schritt

[Tags pushen](tags-pushen.md): der Commit, der Tag und der Push, die die Aufzeichnung zum Remote bringen.

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und Fehlerbehebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Richtlinien, Ansichten und Ereignisse](../richtlinien-ansichten-und-ereignisse.md), [Stand und Verlauf](../status-und-verlauf.md)
- [Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md), [die Audit-Anleitung](../../../bc/6.0/audit.md),
  [audit.ini \[AuditEventSettings\]](../../../beispiele/ini-einstellungen/audit/AuditEventSettings.md)
- [Das Migrations-Repository](../../../anleitungen/migrationen-repositories.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
