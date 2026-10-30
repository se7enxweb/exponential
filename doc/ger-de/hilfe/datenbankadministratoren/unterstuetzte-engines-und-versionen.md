---
title: Unterstützte Engines und Versionen
audience: dba
category: database-administration
order: 3
summary: Die sechs Datenbanken, die Exponential 6.0.x unterstützt, in der offiziellen Reihenfolge des Projekts, mit den drei Versionsnummern, auf die es bei jeder ankommt (die Untergrenze des Einrichtungsassistenten, das praktische Minimum der aktuellen Treiber und die Versionen, die nachweislich funktionieren), dem PHP-Bereich und dem, was die kontinuierliche Integration wirklich testet.
keywords: unterstützte Datenbanken, Versionen, Mindestversion, SQLite 3.33, PostgreSQL 13, PostgreSQL 15, MongoDB 6.0, MariaDB, MySQL 8.0, Oracle 19c, AL32UTF8, Untergrenze des Assistenten, CI, Testabdeckung, PHP 8.0, PHP 8.5, supported databases, versions, minimum version, wizard floor, test coverage
type: help
---
# Unterstützte Engines und Versionen

Diese Seite richtet sich an Datenbankadministratoren, die in einem Entwurfsdokument oder einem Hosting-Angebot angeben
müssen, auf welchen Datenbankserver-Versionen eine Exponential-6.0.x-Website laufen darf. Sie beantwortet: welche Engines
unterstützt werden, in welcher Reihenfolge das Projekt sie empfiehlt, welche Versionsnummern echte Anforderungen und welche
nur historische Untergrenzen sind und wie gut jede Engine getestet wird. Die Zahlen stammen aus
[Datenbankunterstützung](../datenbankunterstuetzung.md), das am Code und an der kontinuierlichen Integration der Linie 6.0.15
geprüft wurde.

## Inhalt

1. [Die offizielle Reihenfolge](#die-offizielle-reihenfolge)
2. [Drei Zahlen je Engine](#drei-zahlen-je-engine)
3. [Die Versionstabelle](#die-versionstabelle)
4. [PHP-Versionen](#php-versionen)
5. [Was das Projekt testet](#was-das-projekt-testet)
6. [Nicht unterstützte Engines](#nicht-unterstützte-engines)
7. [Wie Sie die Unterstützung in eigenen Dokumenten angeben](#wie-sie-die-unterstützung-in-eigenen-dokumenten-angeben)

## Die offizielle Reihenfolge

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlicher Arbeit möglich.

Alle sechs haben einen Treiber im Kernel oder in einer mitgelieferten Erweiterung, einen Eintrag im Einrichtungsassistenten
und einen Weg in den Kommandozeilen-Installern. Die Reihenfolge ist eine Empfehlung für neue Websites, keine Rangliste der
Qualität. MySQL steht zuletzt, weil das Projekt die anderen zuerst empfiehlt, nicht weil es schlechter funktioniert
([Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)).

## Drei Zahlen je Engine

- **Untergrenze des Assistenten**: die älteste Serverversion, die der Einrichtungsassistent annimmt, aus
  `eZSetupDatabaseMap()` in `kernel/setup/ezsetupcommon.php`. Sie stammt aus früheren Releases, damit der Assistent einen
  alten Server nicht sofort ablehnt. Sie ist keine Empfehlung.
- **Praktisches Minimum**: die älteste Version, die die aktuellen Treiber wirklich brauchen, weil sie eine Funktion nutzen.
- **Nachweislich funktionierend**: die Versionen, die das Projekt auf seinem Entwicklungsserver und in der CI betreibt.

Planen Sie mit dem praktischen Minimum und einem Release, das Ihr Hersteller noch mit Sicherheitskorrekturen versorgt.

## Die Versionstabelle

| Engine | Untergrenze des Assistenten | Praktisches Minimum | Warum | Nachweislich funktionierend |
|---|---|---|---|---|
| SQLite 3 | 3.0.1 | **3.33** | Der Treiber schreibt `UPDATE ... JOIN` in `UPDATE ... FROM` um (`eZSQLite3DB::rewriteJoinedUpdate()`) | Bibliothek 3.34.1 in PHP 8.5 |
| PostgreSQL | 8.0 | **13** | Der Installer legt `pgcrypto` an, ab 13 eine vertrauenswürdige Erweiterung; ab 15 muss der Website-Benutzer Eigentümer der Datenbank sein | 13.23; 15 in der CI |
| MongoDB | 4.0 | Server **6.0**, PHP **8.2**, Erweiterung `mongodb` **1.18** | Anforderungen des Adapters ([MongoDB-Unterstützung](../../funktionen/6.0/mongodb-datenbankunterstuetzung.md)) | Server 8.3.13, Erweiterung 2.3.3, PHP 8.5 |
| MariaDB | 4.1.1 (die MySQL-Nummer) | ein unterstütztes Release mit `utf8mb4` und InnoDB | | 10.5.29 |
| Oracle | 19.0 | **19c** | Der Treiber nutzt `STANDARD_HASH` und `OFFSET`/`FETCH`; 11g und älter werden nicht unterstützt | 26ai Free (23.26), oci8 3.4.1 |
| MySQL | 4.1.1 | ein unterstütztes Release mit `utf8mb4` und InnoDB | | 8.0 in der CI |

Prüfen Sie vor einer Installation, was Ihre Server ausführen:

```bash
php --ri sqlite3 | grep -i "library"      # SQLite library inside PHP
psql --version                             # PostgreSQL client; SELECT version(); on the server
mysql --version                            # MySQL or MariaDB client; SELECT VERSION(); on the server
mongosh --eval 'db.version()'              # MongoDB server
```

Auf einer laufenden Website zeigt **Setup > Systeminformationen**, Karte **Datenbank**, die Engine und die Serverversion
([Systeminformationen](../../anleitungen/systeminformationen.md)).

## PHP-Versionen

Exponential 6.0.x läuft auf PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar) (`composer.json`). Die Datenbanktreiber folgen diesem Bereich mit einer
Ausnahme: Der MongoDB-Adapter braucht PHP 8.2 oder neuer. Velocity, der empfohlene Webserver, braucht PHP 8.1. Für Oracle
bedient PECL oci8 3.4 PHP 8.4 und 8.5 und oci8 3.2 PHP 8.1 bis 8.3
([PHP-Erweiterungen je Engine](php-erweiterungen-je-engine.md)).

## Was das Projekt testet

| Engine | In der CI | Wie |
|---|---|---|
| SQLite 3 | Unit-Tests des Treibers | PHPUnit auf PHP 8.1 bis 8.5, Tests in `tests/tests/lib/ezdb/` |
| PostgreSQL | Datenbanktests | Job "Database tests (postgresql, PHP 8.3)" mit einem Dienst `postgres:15` und `pgcrypto` |
| MongoDB | Unit-Tests des Adapters | die Suite `mongodb` ohne Server; die Gruppe `mongodb-live` ist von der CI ausgeschlossen |
| MariaDB | nein | indirekt durch den MySQL-Job abgedeckt (derselbe Treiber) |
| Oracle | nein | Oracle-spezifische Kernel-Tests werden auf anderen Engines übersprungen |
| MySQL | Datenbanktests | Job "Database tests (mysql, PHP 8.3)" mit einem Dienst `mysql:8.0` |

Quelle: `.github/workflows/`, wie in [Datenbankunterstützung](../datenbankunterstuetzung.md#testabdeckung-ci) beschrieben. Wird
Ihre Produktions-Engine nicht in der CI getestet (MariaDB, Oracle), führen Sie das Upgrade zuerst auf einer
Staging-Kopie aus und halten Sie die Wiederherstellung getestet ([Eine Datenbank wiederherstellen](eine-datenbank-wiederherstellen.md)).

## Nicht unterstützte Engines

- **Microsoft SQL Server**: kein Treiber, kein Schema-Handler, kein Installer-Eintrag in 6.0.x. Er ist bekanntermaßen mit
  zusätzlicher Arbeit möglich ([Microsoft SQL Server: Stand für Datenbankadministratoren](stand-von-microsoft-sql-server.md)).
- **Jede andere Engine** (zum Beispiel eine Cloud-Datenbank, die keines dieser Protokolle spricht): nicht unterstützt.
  Verwaltete Dienste, die eine der sechs Engines betreiben (ein verwaltetes PostgreSQL oder MySQL), sind für Exponential
  dieselbe Engine; prüfen Sie, dass sie die Rechte erlauben, die unter
  [Datenbankkonten und Rechte](datenbankkonten-und-rechte.md) aufgeführt sind, und bei PostgreSQL, dass `pgcrypto`
  angelegt werden kann.

## Wie Sie die Unterstützung in eigenen Dokumenten angeben

1. Nennen Sie die Engine und eine Version auf oder über dem praktischen Minimum.
2. Nennen Sie die PHP-Erweiterung und ihre Version (`pgsql`, `mysqli`, `sqlite3`, `mongodb` 1.18+, `oci8` 3.2/3.4).
3. Nennen Sie den Zeichensatz (`UTF8`, `utf8mb4`, `AL32UTF8`; siehe [Zeichensätze und Sortierfolgen](zeichensaetze-und-sortierfolgen.md)).
4. Sagen Sie, ob die Engine in der CI des Projekts ist, und falls nicht, was Sie selbst testen.
5. Verlinken Sie [Datenbankunterstützung](../datenbankunterstuetzung.md) als Aussage des Projekts selbst und nennen Sie das
   Datum, an dem Sie sie geprüft haben.

## Verwandte Seiten

- [Eine Engine wählen](eine-engine-waehlen.md)
- [PHP-Erweiterungen je Engine](php-erweiterungen-je-engine.md)
- [Datenbanken in der offiziellen Reihenfolge](../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md) (für Hosting-Unternehmen)
- [Kapitel 2, Anforderungen](../../installation/02-voraussetzungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
