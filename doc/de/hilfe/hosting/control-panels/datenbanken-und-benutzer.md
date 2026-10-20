---
title: Datenbanken und Benutzer
audience: hosting, admin
category: hosting
order: 90
summary: Die Datenbank und ihren Benutzer für eine Exponential-Website anlegen, Engine für Engine in der offiziellen Reihenfolge (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), mit den Rechten, die jeder Treiber braucht, dem Zeichensatz, wie die Verbindung in die Einstellungen geschrieben wird und was die Datenbankseite eines Panels erlauben muss.
keywords: Datenbank, Datenbankbenutzer, Rechte, Grants, SQLite, PostgreSQL, pgcrypto, Eigentümer, MongoDB, readWrite, dbAdmin, MariaDB, MySQL, utf8mb4, InnoDB, Oracle, ora-grant, AL32UTF8, DatabaseSettings, Panel, Shared Hosting, database, database user, privileges, grants, owner, panel, shared hosting
type: help
---
# Datenbanken und Benutzer

Diese Seite ist für Administratoren und Support-Mitarbeitende bei Hosting-Anbietern, die Datenbanken und Datenbankbenutzer für
Exponential-Websites anlegen, von Hand oder über ein Panel. Sie beantwortet für jede der sechs unterstützten Engines in der
offiziellen Reihenfolge, was vor der Installation existieren muss, welche Rechte der Benutzer der Website braucht und wie die
Verbindung in die Einstellungen der Website gelangt.

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich.
> ([Datenbankunterstützung](../../datenbankunterstuetzung.md))

## Inhalt

1. [Vor der Installation](#vor-der-installation)
2. [1. SQLite 3](#1-sqlite-3)
3. [2. PostgreSQL](#2-postgresql)
4. [3. MongoDB](#3-mongodb)
5. [4. MariaDB](#4-mariadb)
6. [5. Oracle](#5-oracle)
7. [6. MySQL](#6-mysql)
8. [Microsoft SQL Server](#microsoft-sql-server)
9. [Wo die Verbindung gespeichert wird](#wo-die-verbindung-gespeichert-wird)
10. [Was die Datenbankseite eines Panels erlauben muss](#was-die-datenbankseite-eines-panels-erlauben-muss)

## Vor der Installation

Für einen Datenbankserver legen Sie vor der Installation die Datenbank und einen Benutzer an, der darin Tabellen anlegen darf; die
Installer legen weder Datenbanken noch Benutzer an ([2.6 Datenbanken](../../../installation/02-voraussetzungen.md#26-datenbanken)).

| Engine | Eine Datenbank anlegen? | Einen Benutzer anlegen? | PHP-Erweiterung |
|---|---|---|---|
| 1. SQLite 3 | nein (eine Datei, vom Installer angelegt) | nein | `sqlite3` |
| 2. PostgreSQL | ja, im Eigentum des Benutzers der Website | ja | `pgsql` |
| 3. MongoDB | nein (entsteht beim ersten Schreiben) | ja, in der Datenbank der Website | `mongodb` + Bibliothek `mongodb/mongodb` |
| 4. MariaDB | ja, `utf8mb4` | ja | `mysqli` |
| 5. Oracle | einen Schema-Benutzer | ja, mit dem Grant-Skript der Erweiterung | `oci8` |
| 6. MySQL | ja, `utf8mb4` | ja | `mysqli` |

Seit 6.0.15 arbeitet der Setup-Assistent nur mit Ihrer eigenen Datenbank: Sie brauchen nur noch Rechte auf Ihre eigene Datenbank
([Eine saubere Installation, die zu Shared Hosting passt](../../../../en/us/features/6.0/clean-install-defaults.md)).

## 1. SQLite 3

Kein Server, kein Benutzer, keine Rechte. Die Datenbank ist eine Datei unter `var/storage/sqlite3/`. Die Zugriffskontrolle ist das
Dateisystem: Die Datei und ihr Verzeichnis gehören dem Benutzer der Website, und jeder Prozess, der schreibt (Pool, Velocity-Worker,
Cron), läuft als dieser Benutzer oder teilt seine Gruppe
([9.2 Dateien, Eigentum und gemischte Benutzer](../../../installation/09-datenbanken.md#dateien-eigentümer-und-gemischte-benutzer)). In einem Panel
muss nichts angelegt werden; der Installer schreibt die Datei.

## 2. PostgreSQL

Aus [9.4](../../../installation/09-datenbanken.md#94-postgresql):

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

- Den Benutzer zum **Eigentümer** der Datenbank zu machen, ist wichtig: Ab PostgreSQL 15 dürfen gewöhnliche Benutzer im Schema
  `public` einer Datenbank, die ihnen nicht gehört, keine Tabellen mehr anlegen.
- Der Treiber braucht `pgcrypto`. Der Installer führt `CREATE EXTENSION IF NOT EXISTS pgcrypto` aus, wenn der Server sie mitliefert;
  als vertrauenswürdige Erweiterung darf der Eigentümer der Datenbank sie anlegen. Sonst, als Eigentümer oder Superuser:

```bash
sudo -u postgres psql -d exponential -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
```

Praktisches Minimum: PostgreSQL 13 (`pgcrypto` ist ab 13 vertrauenswürdig).

> **Hinweis**
> Wenn Ihr Panel PostgreSQL-Datenbanken anlegt, die einer anderen Rolle als dem Benutzer der Website gehören, kann der Benutzer der
> Website auf PostgreSQL 15 und später keine Tabellen anlegen. Lesen Sie in der Dokumentation Ihres Panels nach, wie es das
> Eigentum vergibt.

## 3. MongoDB

Aus [9.5](../../../installation/09-datenbanken.md#95-mongodb): Legen Sie den Benutzer **in der Datenbank der Website** an, weil MongoDB die
Datenbank im Pfad der URI als Authentifizierungsdatenbank nutzt, wenn kein `authSource` angegeben ist:

```bash
mongosh --eval '
  db.getSiblingDB("exponential").createUser({
    user: "exponential",
    pwd: passwordPrompt(),
    roles: [ { role: "readWrite", db: "exponential" }, { role: "dbAdmin", db: "exponential" } ]
  })'
```

Der Benutzer ist erforderlich, auch auf einem Testserver, der keine Zugriffskontrolle durchsetzt. Anforderungen: PHP 8.2 oder später,
Server 6.0 oder später, PHP-Erweiterung `mongodb` 1.18 oder später. Lesen Sie die [Grenzen von MongoDB](../../datenbankunterstuetzung.md#grenzen-von-mongodb),
bevor Sie es anbieten.

## 4. MariaDB

Derselbe Treiber wie bei MySQL (`mysqli`); aus [9.3](../../../installation/09-datenbanken.md#93-mysql-und-mariadb):

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

- Der Benutzer braucht kein globales Recht, kein `SUPER` und kein Recht zum Auflisten von Datenbanken. `LOCK TABLES` und
  `CREATE TEMPORARY TABLES` brauchen der Treiber und der Kernel.
- Legen Sie die Datenbank in `utf8mb4` an; sonst hält der Assistent mit der Meldung an, dass die Datenbank nicht genutzt werden kann,
  weil sie mit einem anderen Zeichensatz angelegt wurde ([2.6](../../../installation/02-voraussetzungen.md#26-datenbanken)).
- Der Installer legt InnoDB-Tabellen an, wenn der Server es anbietet; Transaktionen brauchen es.

## 5. Oracle

Oracle 19c oder später über die Erweiterung `ezoracle`, Zeichensatz der Datenbank AL32UTF8
([9.6](../../../installation/09-datenbanken.md#96-oracle-19c-und-neuer-ezoracle)). Die Erweiterung gibt das SQL für einen DBA aus:

```bash
ORACLE_NEW_PASSWORD='<a strong password>' php extension/ezoracle/bin/php/ora-grant.php exponential - USERS
```

`-` nimmt das Passwort aus `ORACLE_NEW_PASSWORD`, sodass es nicht in der Prozessliste erscheint; `USERS` ist der Tablespace.
Dasselbe gibt es als `php bin/php/console ext:ezoracle:ora-grant`. Für die Befehle zu Zustand und Berichten vergeben Sie außerdem
`SELECT_CATALOG_ROLE`.

## 6. MySQL

Genau wie 4. MariaDB: derselbe Treiber, dasselbe `CREATE DATABASE ... CHARACTER SET utf8mb4`, dieselben Rechte pro Datenbank.
MySQL steht in der Reihenfolge zuletzt, nicht weil es weniger gut funktioniert, sondern weil das Projekt für neue Websites zuerst die
anderen empfiehlt ([Datenbankunterstützung](../../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)). Es wird in der CI mit
MySQL 8.0 getestet.

## Microsoft SQL Server

In 6.0.x gibt es keinen Treiber. Es ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich; was geschrieben werden müsste, steht
in [Datenbankunterstützung](../../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand). Bieten Sie es nicht als
Datenbankoption für Exponential-Websites an.

## Wo die Verbindung gespeichert wird

Die Installer schreiben die Verbindung in `settings/override/site.ini.append.php`, Block `[DatabaseSettings]`. Zum Beispiel für
2. PostgreSQL ([Die Einstellungen](../../datenbankunterstuetzung.md#die-einstellungen-databasesettings-in-siteini)):

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=exponential
Password=<the password>
Database=exponential
```

- Die Datei enthält das Passwort: Halten Sie `settings/override` auf dem Modus `0640` oder strenger und aus der Versionsverwaltung
  und aus Backups heraus, die andere lesen können ([13.3](../../../installation/13-sicherheitshaertung.md#133-geheimnisse-in-settingsoverride)).
- Wenn ein Panel ein Datenbankpasswort ändert, ändern Sie es auch dort, leeren Sie dann den INI-Cache und starten Sie die Worker von
  Velocity neu, wenn Velocity die Website ausliefert:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

## Was die Datenbankseite eines Panels erlauben muss

| Anforderung | Engines |
|---|---|
| Eine Datenbank mit gewähltem Zeichensatz (`utf8mb4`) anlegen | 4. MariaDB, 6. MySQL |
| Der Benutzer der Website ist Eigentümer der Datenbank oder darf Tabellen und die Erweiterung `pgcrypto` anlegen | 2. PostgreSQL |
| Ein Benutzer, der in der eigenen Datenbank der Website mit `readWrite` und `dbAdmin` definiert ist | 3. MongoDB |
| Rechte pro Datenbank einschließlich `LOCK TABLES` und `CREATE TEMPORARY TABLES` | 4. MariaDB, 6. MySQL |
| Nichts (nur beschreibbares `var/`) | 1. SQLite 3 |

Wenn Ihr Panel eine Zeile nicht erfüllen kann, eignet sich diese Engine auf diesem Panel nicht für die Selbstbedienung; bieten Sie
1. SQLite 3 an, oder legen Sie die Datenbank für den Kunden an.

## Verwandte Seiten

- [Control Panels: Überblick](README.md)
- [Datenbanken in der offiziellen Reihenfolge](../grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
- [Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)
- [Kapitel 9: Datenbanken](../../../installation/09-datenbanken.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
