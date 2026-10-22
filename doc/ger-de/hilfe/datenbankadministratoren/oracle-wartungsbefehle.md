---
title: Wartungsbefehle und Cronjobs für Oracle
audience: dba
category: database-administration
order: 21
summary: Die Wartungswerkzeuge, die die Erweiterung ezoracle einem Oracle-DBA gibt - die Konsolenbefehle ext:ezoracle (health, gather-stats, recompile, purge-recyclebin, sequence-sync, schema-diff, indexes, report, datapump, ci-indexes), die Skripte in extension/ezoracle/bin/php und die Cronjob-Teile ezoracle und ezoraclehealth mit ihren Schaltern.
keywords: Oracle, ezoracle, ext:ezoracle, health, gather-stats, DBMS_STATS, recompile, purge-recyclebin, sequence-sync, schema-diff, indexes, report, datapump, DBMS_DATAPUMP, expdp, impdp, ci-indexes, ora-update-seqs, mysql2oracle, oracleclusterbinarypurge, Cronjob, ezoraclehealth, CronjobSettings, Wartung, maintenance, cronjob
type: help
---
# Wartungsbefehle und Cronjobs für Oracle

Diese Seite richtet sich an Oracle-Datenbankadministratoren, die ein Exponential-Schema gesund halten. Die Erweiterung
`ezoracle` liefert Konsolenbefehle, die die übliche Oracle-Wartung kapseln, und Cronjobs, die sie nach Plan ausführen. Diese
Seite beantwortet: welche Befehle es gibt, was jeder tut, wie Sie sie einplanen und wohin sie berichten. Quelle: die
Abschnitte "Console commands" und "Cronjobs" von `extension/ezoracle/INSTALL` (Version 2.3.7) und
[9.6 Wartung](../../installation/09-datenbanken.md#wartung).

## Inhalt

1. [Wie sich die Befehle verhalten](#wie-sich-die-befehle-verhalten)
2. [Die Konsolenbefehle](#die-konsolenbefehle)
3. [Skripte in bin/php](#skripte-in-binphp)
4. [Cronjobs](#cronjobs)
5. [Eine Routine für eine Oracle-Website](#eine-routine-für-eine-oracle-website)
6. [Checkliste](#checkliste)

## Wie sich die Befehle verhalten

```bash
php bin/php/console ext:ezoracle:<command> [-s <siteaccess>] [options]
```

Jeder Befehl hat `--help`, gibt je Prüfung eine Zeile PASS, WARN, FAIL oder INFO aus und endet mit Exit-Status 1, wenn eine
Prüfung gescheitert ist, sodass er ein Skript absichern kann. Befehle, die etwas ändern, nehmen `--dry-run`. `V$`- und
DBA-Views brauchen `SELECT_CATALOG_ROLE`; ohne sie sagen die betroffenen Prüfungen das.

## Die Konsolenbefehle

| Befehl | Was er tut |
|---|---|
| `health` | Verbindung, Versionen, Zeichensatz, ungültige Objekte, Tablespaces, Sessions, blockierende Sperren, Papierkorb, Sequenzen, Statistiken |
| `gather-stats` | `DBMS_STATS.GATHER_SCHEMA_STATS` (`[StatisticsSettings]`) |
| `recompile` | `DBMS_UTILITY.COMPILE_SCHEMA` für ungültige Objekte |
| `purge-recyclebin` | `PURGE RECYCLEBIN` |
| `sequence-sync` | Sequenzen hinter den IDs ihrer Tabellen; `--fix` setzt sie weiter |
| `schema-diff` | Die Datenbank gegen `share/db_schema.dba` (`--file=<dba>`), mit dem Upgrade-SQL |
| `indexes` | Indexbericht; `--rebuild [--index=<name>] [--online]`; `--monitor-on`, `--monitor-off`, `--usage` |
| `report` | `--sizes` (Tabellen mit Index- und LOB-Platz), `--top-sql` (aus `V$SQL`) |
| `datapump` | `--export=<file.dmp>` und `--import=<file.dmp>` über `DBMS_DATAPUMP` in das Verzeichnisobjekt aus `[DataPumpSettings] Directory` (Standard `DATA_PUMP_DIR`; der Benutzer braucht READ und WRITE darauf); `--table-exists-action`, `--remap-schema` |
| `ci-indexes` | Linguistische Indizes für `OracleCaseInsensitive` (`--create`, `--drop`) |

Die Serverwerkzeuge funktionieren ebenfalls: `expdp <user>@<db> schemas=<user> directory=DATA_PUMP_DIR dumpfile=<file.dmp>`
und `impdp` für die Wiederherstellung ([Sicherungen je Engine](sicherungen-je-engine.md)).

## Skripte in bin/php

`extension/ezoracle/bin/php/` enthält die Skripte hinter den Befehlen und einige weitere:

| Skript | Verwendung |
|---|---|
| `ora-grant.php` | Gibt die Rechte für ein neues Konto aus ([Oracle einrichten](oracle-einrichten.md#das-konto-anlegen)) |
| `ora-update-seqs.php` | Setzt Sequenzen über einen Login-String weiter, wie `sequence-sync --fix` |
| `mysql2oracle-schema.php`, `mysql2oracle-data.php` | Eine MySQL-Website nach Oracle umziehen; auch `ext:ezoracle:mysql2oracle-schema` und `-data` ([Eine Website auf eine andere Engine umziehen](zwischen-engines-wechseln.md)) |
| `ora-drop-schema.php` | Löscht die Objekte des Schemas; zerstörerisch, lesen Sie zuerst sein `--help` |
| `oracleclusterbinarypurge.php` | Entfernt abgelaufene Dateien des DFS-Clusters, wenn das Oracle-Backend verwendet wird ([Clustering](clustering-dfs-und-die-datenbank.md)) |

Das `INSTALL` der Erweiterung beschreibt außerdem `extension/ezoracle/bin/shell/ora-initialize.sh`, einen älteren
automatisierten Initialisierungsweg. Die aktuellen Installer (`exp:install --db=oracle`, der Assistent, der Kickstarter) sind
der dokumentierte Weg für eine neue Website.

## Cronjobs

Die Erweiterung fügt in ihrer eigenen `settings/cronjob.ini.append.php` Cronjob-Teile hinzu:

| Teil | Skripte |
|---|---|
| `ezoracle` | Statistiken, Papierkorb, Sequenzen, Gesundheit |
| `ezoraclehealth` | nur Gesundheit |
| `ezoracle_statistics`, `ezoracle_recyclebin`, `ezoracle_sequences` | je ein Job |

Jeder Job tut nichts, bis er in `settings/override/ezoracle.ini.append.php` eingeschaltet ist:

```ini
[CronjobSettings]
GatherStatistics=enabled
HealthMonitor=enabled
HealthMailReceivers[]=dba@example.com
PurgeRecycleBin=enabled
SequenceCheck=enabled
SequenceFix=disabled
```

und in der Crontab, zum Beispiel:

```text
15 3 * * *  cd /path/to/installation && php runcronjobs.php -q -s <admin siteaccess> ezoracle
0 * * * *   cd /path/to/installation && php runcronjobs.php -q -s <admin siteaccess> ezoraclehealth
```

Berichte gehen nach `var/log/ezoracle-<job>.log`; der Gesundheitsmonitor schickt seinen Bericht an `HealthMailReceivers[]`,
wenn eine Prüfung warnt oder scheitert. Verwenden Sie eine Teamadresse, keine persönliche.

## Eine Routine für eine Oracle-Website

| Wann | Was |
|---|---|
| Stündlich | `ezoraclehealth` |
| Nächtlich | `ezoracle` (Statistiken, Papierkorb, Sequenzprüfung) |
| Nach einem Upgrade | `schema-diff`, `recompile`, `sequence-sync` |
| Nach einem Import oder einer Wiederherstellung | `sequence-sync --fix --dry-run`, dann ohne `--dry-run` |
| Monatlich | `report --sizes`, `indexes --usage` |

## Checkliste

- [ ] `SELECT_CATALOG_ROLE` vergeben, sodass `health` und `report` alles sehen.
- [ ] Cron-Schalter im Override eingeschaltet; beide Crontab-Zeilen installiert.
- [ ] `HealthMailReceivers[]` ist ein Team-Postfach.
- [ ] Das Verzeichnisobjekt für Data Pump existiert, und das Konto kann es lesen und schreiben.

## Verwandte Seiten

- [Oracle mit ezoracle einrichten](oracle-einrichten.md)
- [Oracle: persistente Verbindungen, DRCP und Tuning](oracle-verbindungen-und-tuning.md)
- [Aufräumen: Cronjobs und Warteschlangentabellen](aufraeumen-cronjobs-und-warteschlangen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
