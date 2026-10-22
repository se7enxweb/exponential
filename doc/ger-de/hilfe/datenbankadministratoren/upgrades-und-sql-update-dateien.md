---
title: Upgrades und die SQL-Update-Dateien
audience: dba
category: database-administration
order: 30
summary: Wie die Datenbank-Update-Dateien von Exponential je Engine und Version in update/database geordnet sind, welche Kette eine Datenbank von 3.10 bis 6.0.15 durchläuft, die Regeln für ihre Anwendung, die Verzeichnisse 6.12, 7.2 und 7.3, die nicht angewendet werden dürfen, die Cluster-Dateien, wie Sie die Versionszeile in ezsite_data lesen und wie Sie prüfen, ob die Update-Dateien vollständig sind.
keywords: Upgrade, Update-Dateien, dbupdate, update/database, mysql, postgresql, sqlite, ezoracle, MongoDB, Kette, ezsite_data, ezpublish-version, 6.0.15stable, 6.12, 7.2, 7.3, Cluster-Update, unstable, exp:checkdbfiles, SET default_storage_engine, upgrade, update files, chain, cluster update
type: help
---
# Upgrades und die SQL-Update-Dateien

Diese Seite richtet sich an Datenbankadministratoren, die den Datenbankteil eines Exponential-Upgrades anwenden. Sie
beantwortet: wo die Update-Dateien liegen, welche eine Datenbank braucht, die Regeln für ihre Anwendung, welche Verzeichnisse
nie angewendet werden dürfen und wie Sie erkennen, auf welcher Version eine Datenbank steht. Das Kapitel Schritt für Schritt
ist [Kapitel 11, Aktualisieren](../../installation/11-aktualisieren.md); die Anleitung für Administratoren ist
[Aktualisieren](../../anleitungen/aktualisieren.md).

## Inhalt

1. [Auf welcher Version steht die Datenbank?](#auf-welcher-version-steht-die-datenbank)
2. [Wo die Update-Dateien liegen](#wo-die-update-dateien-liegen)
3. [Die Kette](#die-kette)
4. [Regeln](#regeln)
5. [Verzeichnisse, die Sie nicht anwenden dürfen](#verzeichnisse-die-sie-nicht-anwenden-dürfen)
6. [Hinweise je Engine](#hinweise-je-engine)
7. [Die Update-Dateien selbst prüfen](#die-update-dateien-selbst-prüfen)
8. [Checkliste](#checkliste)

## Auf welcher Version steht die Datenbank?

Der Code und die Datenbank tragen jeweils eine Version; nach einem korrekten Upgrade stimmen sie überein.

```bash
php bin/php/console --version
mysql -u USER -p DATABASE -e "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
psql -U USER DATABASE -c "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
sqlite3 var/storage/sqlite3/exponential.db "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
```

Eine aktuelle Website gibt `ezpublish-version` `6.0.15stable` und `ezpublish-release` `1` aus. Die Zeilennamen behalten ihre
historische Schreibweise mit Absicht: Sie sind Bezeichner, in die jede Update-Datei schreibt. **Setup > Systeminformationen**
zeigt ebenfalls beide Versionen ([11.2](../../installation/11-aktualisieren.md#112-welche-version-betreiben-sie)).

## Wo die Update-Dateien liegen

| Engine | Verzeichnis | Schritte |
|---|---|---|
| MySQL, MariaDB | `update/database/mysql/` | 3.10 auf 4.0 ... 5.4, dann `6.0/dbupdate-5.4.0-6.0.0.sql`, dann `6.0/dbupdate-6.0.0-6.0.15.sql` |
| PostgreSQL | `update/database/postgresql/` | dieselbe Kette; der Schritt 5.4 ist `6.0/dbupdate-5.4-to-6.0.sql`, dann `6.0/dbupdate-6.0.0-6.0.15.sql` |
| SQLite | `update/database/sqlite/` | nur `6.0/dbupdate-6.0.0-6.0.15.sql` (die SQLite-Unterstützung beginnt mit 6.0.1) |
| Oracle | `extension/ezoracle/update/database/` | `ezpublish/` für die Kernel-Kette bis 5.3, `ezoracle/` für die eigenen Tabellen des Treibers |
| MongoDB | keines | die Audit-Collections von 6.0.15 kommen aus `createaudittables.php` |

## Die Kette

Jede Datei bringt eine Datenbank **von** einer Version **auf** die nächste. Eine Datenbank durchläuft die Kette der Reihe
nach, jede Datei einmal:

| Sie betreiben | Weg |
|---|---|
| 3.10 bis 5.3 | die alte Kette bis 5.4, dann auf 6.0.0, dann auf 6.0.15 |
| 5.4.0 oder 5.90 | auf 6.0.0, dann auf 6.0.15 |
| 6.0.0 bis 6.0.14 | `dbupdate-6.0.0-6.0.15.sql` |

Die vollständige Tabelle der Dateien von 4.0 bis 5.4 steht in [11.3](../../installation/11-aktualisieren.md#113-die-update-dateien).
Wenden Sie eine Datei mit dem Client der Datenbank an:

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
psql -U USER -d DATABASE -f update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql
sqlite3 var/storage/sqlite3/exponential.db < update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql
```

## Regeln

- **Zuerst sichern** und wissen, wie Sie wiederherstellen ([Eine Datenbank wiederherstellen](eine-datenbank-wiederherstellen.md)).
- **Der Reihe nach, jede einmal.** Eine Anweisung, die scheitert, weil eine Spalte oder Tabelle schon existiert, bedeutet, dass
  die Datei schon einmal angewendet wurde: Halten Sie an und vergleichen Sie mit der Sicherung.
- **Eine Datei, die mittendrin scheitert**: Stellen Sie die Sicherung wieder her und wenden Sie die Datei von vorn an;
  reparieren Sie nicht von Hand.
- **Cluster-Dateien**: Verwendet die Installation den DFS-Cluster, wenden Sie auch die Datei `dbupdate-cluster-...` desselben
  Schritts an (MySQL: 4.3, 4.7, 5.2, 5.4).
- **`unstable/`-Verzeichnisse** enthalten Dateien von Vorabversionen; wenden Sie sie nie auf eine Website an, die ein finales
  Release betrieben hat.
- **Je Engine**: Eine Website, die auf eine andere Engine umgezogen ist, verwendet die Update-Dateien der Engine, auf der sie
  jetzt läuft.

## Verzeichnisse, die Sie nicht anwenden dürfen

`update/database/<engine>/6.12/`, `7.2/` und `7.3/` stammen aus dem Upstream-Legacy-Kernel von 2016 bis 2018, nummeriert nach
einer Version eines Symfony-Kernels, nicht nach einem Legacy-Release. Als Dateien angewendet, würde jede die Versionszeile durch
eine Zahl ersetzen, die keine Exponential-Version ist. Ihre Änderungen (das breitere `ezuser.password_hash`,
`ezcontentobject_trash.trashed` und auf PostgreSQL die Sequenznamen `<table>_<column>_seq`) stecken bereits in den Dateien 6.0
([11.3](../../installation/11-aktualisieren.md#die-verzeichnisse-612-72-und-73)).

## Hinweise je Engine

| Engine | Hinweis |
|---|---|
| MySQL, MariaDB | Dateien beginnen mit `SET default_storage_engine=InnoDB;`. Kopien von vor Oktober 2026 enthielten `SET storage_engine=InnoDB;`, entfernt in MySQL 5.7.5 und MariaDB 12.0: Nehmen Sie die aktuellen Dateien. Tabellen müssen UTF-8 und InnoDB sein ([MySQL und MariaDB: utf8mb4](mysql-und-mariadb-zeichensatz-und-innodb.md)). |
| PostgreSQL | Abgesicherte Schritte sind `DO`-Blöcke (PostgreSQL 9.0+); `digest()` aus `pgcrypto` muss existieren. |
| SQLite | Jedes `ALTER TABLE` fügt eine Spalte hinzu, und SQLite hat kein `ADD COLUMN IF NOT EXISTS`: Ein zweiter Lauf scheitert mit "duplicate column name". Führen Sie die Datei einmal aus, nach einer Online-Sicherung. |
| Oracle | Die eigenen Dateien der Erweiterung bis 5.3; die Audit-Tabellen von 6.0.15 aus `createaudittables.php`. |
| MongoDB | Kein SQL; die Upgrade-Prüfung gibt die `mongosh`-Befehle für fehlende Collections aus. |

## Die Update-Dateien selbst prüfen

```bash
php bin/php/console exp:checkdbfiles --no-verify-branches
```

Es prüft die Update-Dateien gegen den Upgrade-Weg, jedes Verzeichnis von 4.0 bis 7.3 auf jeder Engine, und gibt je Problem
eine Zeile aus: `!` für eine fehlende Datei, `?` für eine überzählige. Keine Ausgabe bedeutet, dass die Dateien vollständig
sind. Ohne `--no-verify-branches` versucht es außerdem ein stillgelegtes SVN-Repository und gibt eine Zeile `C` aus;
ignorieren Sie sie oder fügen Sie die Option hinzu.

## Checkliste

- [ ] Versionszeile vorher und nachher gelesen.
- [ ] Sicherung erstellt und Wiederherstellung bekannt.
- [ ] Nur die Dateien Ihrer Engine und Ihres Weges, der Reihe nach, jede einmal.
- [ ] Keine Datei aus `6.12/`, `7.2/`, `7.3/` oder `unstable/` angewendet.
- [ ] Cluster-Dateien angewendet, wo der Cluster verwendet wird.
- [ ] `exp:checkdbfiles` gibt nichts aus.

## Verwandte Seiten

- [Das Datenbank-Update auf 6.0.15 anwenden](das-datenbank-update-6-0-15-anwenden.md)
- [Das Schema nach einem Upgrade prüfen](das-schema-nach-einem-upgrade-pruefen.md)
- [Kunden-Websites aktualisieren](../hosting/betrieb/kunden-websites-aktualisieren.md) (für Hosting-Unternehmen)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
