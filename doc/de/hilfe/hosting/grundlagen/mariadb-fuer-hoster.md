---
title: MariaDB für Hoster
audience: hosting, admin
category: hosting
order: 100
summary: MariaDB als Datenbank einer Exponential-Website aus Sicht eines Hosters - der gemeinsame Treiber mysqli, utf8mb4 und InnoDB, die Rechte, die der Benutzer einer Website braucht, Einstellungen, Installation, der Zeichensatz der Verbindung, Backups und die üblichen Probleme.
keywords: MariaDB, mysqli, ezmysqli, MySQL Improved, utf8mb4, utf8mb3, InnoDB, Rechte, CREATE DATABASE, mariadb-dump, mysqldump, single-transaction, UseSlaveServer, Replikate, Zeichensatz der Verbindung, Datenbanken im Panel, Hosting, grants, replicas, connection charset, panel databases, hosting
type: help
---
# MariaDB für Hoster

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei einem Hosting-Anbieter, die MariaDB-Datenbanken
für Exponential-Websites bereitstellen, die übliche Datenbank, die ein Hosting-Panel anlegt. Sie beantwortet: wie
MariaDB angesteuert wird, wie Sie die Datenbank und den Benutzer einer Website anlegen, worauf Sie bei Zeichensätzen
achten müssen und wie Sie sichern. MariaDB ist die vierte in der offiziellen Reihenfolge und der MySQL-kompatible
Server, den das Projekt bevorzugt. Quellen: [Datenbankunterstützung](../../../../en/us/help/database-support.md#4-mariadb) und
[Kapitel 9.3](../../../../en/us/install/09-databases.md#93-mysql-and-mariadb).

## Inhalt

1. [Ein Treiber für MariaDB und MySQL](#ein-treiber-für-mariadb-und-mysql)
2. [Versionen](#versionen)
3. [Eine Datenbank pro Website anlegen](#eine-datenbank-pro-website-anlegen)
4. [Einstellungen und Installation](#einstellungen-und-installation)
5. [Zeichensätze](#zeichensätze)
6. [Backups](#backups)
7. [Probleme und ihre Ursachen](#probleme-und-ihre-ursachen)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Ein Treiber für MariaDB und MySQL

MariaDB läuft mit demselben Treiber wie MySQL, `eZMySQLiDB` (`lib/ezdb/classes/ezmysqlidb.php`), auf der
PHP-Erweiterung `mysqli`. Alles auf [MySQL für Hoster](mysql-fuer-hoster.md) gilt; diese Seite behandelt, was ein Hoster
tun muss und was sich unterscheidet.

- Der Setup-Assistent führt es als „MySQL Improved“.
- `exp:install --db=mariadb` ist dasselbe wie `--db=mysql`.
- Der Kickstarter nimmt `[database_choice] Type=mysqli` (`mysql` wird akzeptiert).
- MariaDB ist nicht in der CI des Projekts; der Job für MySQL 8.0 testet denselben Treiber, und MariaDB 10.5 läuft auf
  dem Entwicklungsserver des Projekts ([Testabdeckung](../../../../en/us/help/database-support.md#test-coverage-ci)).

## Versionen

Die Untergrenze des Assistenten ist 4.1.1 (die MySQL-Nummer in `eZSetupDatabaseMap()`), was keine Empfehlung ist.
Betreiben Sie ein Release, das Ihr Hersteller noch mit Sicherheitskorrekturen versorgt, mit `utf8mb4` und InnoDB
([Versionen](../../../../en/us/help/database-support.md#versions-checked-against-the-code)).

## Eine Datenbank pro Website anlegen

Die Installer legen weder Datenbanken noch Benutzer an. Es ist kein globales Recht nötig:

```sql
CREATE DATABASE <customer> CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '<customer>'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON <customer>.* TO '<customer>'@'localhost';
```

`LOCK TABLES` und `CREATE TEMPORARY TABLES` stehen in der Liste, weil `lock()` des Treibers `LOCK TABLES` verwendet und
der Kernel für manche Vorgänge temporäre Tabellen anlegt. Es ist kein `SUPER` und kein Recht zum Auflisten von
Datenbanken nötig.

> **Hinweis**
> Wenn ein Control Panel die Datenbank anlegt, prüfen Sie seinen Standardzeichensatz und die Rechte, die es vergibt,
> gegen die Anweisungen oben; wie Sie sie setzen, steht in der Dokumentation Ihres Panels. Der Assistent lehnt eine
> Datenbank ab, deren Zeichensatz nicht UTF-8 ist ([Datenbanken und Benutzer in Control Panels](../control-panels/datenbanken-und-benutzer.md)).

## Einstellungen und Installation

```ini
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
# empty: 3306
Port=
User=<customer>
Password=<the password>
Database=<customer>
# a Unix socket instead of host and port
Socket=disabled
Charset=utf-8
UsePersistentConnection=disabled
```

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=mariadb \
    --db-host=127.0.0.1 --db-name=<customer> --db-user=<customer> --allow-root-user
```

Der Installer legt InnoDB-Tabellen an, wenn der Server InnoDB anbietet; Transaktionen brauchen es. Für eine ältere
Installation listet `php bin/php/ezconvertmysqltabletype.php --list` die Tabellentypen auf, und `--newtype=InnoDB`
konvertiert sie.

**Lese-Replikate.** `UseSlaveServer=enabled` mit `SlaveServerArray[]` und den zugehörigen Schlüsseln schickt
Lesezugriffe an ein Replikat, das pro Anfrage zufällig gewählt wird; Schreibzugriffe gehen an `Server`. MySQL und MariaDB
sind die einzigen Engines mit eingebauter Aufteilung von Lesezugriffen ([Kapitel 9.3, Replikate](../../../../en/us/install/09-databases.md#replicas)).

## Zeichensätze

- Legen Sie die Datenbank in `utf8mb4` an. Der Installer legt seine Tabellen ohne eigenen Zeichensatz an, sodass sie den
  Standard der Datenbank übernehmen.
- Die Verbindung: `Charset=utf-8` (der Standard) wird als `utf8` an den Server gesendet, was MariaDB als das
  Drei-Byte-`utf8mb3` behandelt. Zeichen außerhalb der Basic Multilingual Plane, etwa Emoji, können nicht durch die
  Verbindung gehen, auch wenn die Tabellen `utf8mb4` sind. Halten Sie die Datenbank trotzdem in `utf8mb4`.
- Wenn sich der Zeichensatz der Verbindung nicht setzen lässt, läuft der Treiber weiter und protokolliert „could not
  set the connection charset“, aber nur, wenn Warnungen protokolliert werden (`site.ini [DebugSettings] AlwaysLog[]=warning`).
  Text kommt dann mit `?` anstelle von Zeichen zurück.

Die Seite **Systeminformationen** warnt, wenn der Zeichensatz der Verbindung nicht UTF-8 ist.

## Backups

```bash
mariadb-dump --single-transaction -u <customer> -p <customer> > <customer>.sql
mariadb -u <customer> -p <customer> < <customer>.sql
```

`--single-transaction` ergibt einen konsistenten Dump von InnoDB-Tabellen, ohne sie zu sperren; der ältere Name
`mysqldump` funktioniert auch. Befehle mit einem Passwort auf der Kommandozeile zeigen es anderen Benutzern der
Maschine; bevorzugen Sie die Abfrage oder eine Optionsdatei. Ein vollständiges Backup enthält außerdem `var/storage`,
`settings/override` und `settings/siteaccess`.

## Probleme und ihre Ursachen

| Symptom | Ursache |
|---|---|
| Die Fehlerseite, dass die Datenbank nicht erreichbar ist | falscher Host, Benutzer, falsches Passwort oder falsche Datenbank |
| Der Assistent lehnt den Zeichensatz der Datenbank ab | die Datenbank wurde in `latin1` oder einem anderen Ein-Byte-Zeichensatz angelegt; legen Sie sie in `utf8mb4` neu an |
| Text mit `?` an der Stelle von Buchstaben mit Akzent | die Verbindung ist auf den Standardzeichensatz des Servers zurückgefallen |
| Die Tabellen einer Erweiterung sind nicht InnoDB | mit `ezconvertmysqltabletype.php --newtype=InnoDB` konvertieren |
| Langsame Seiten | `[DatabaseSettings] SQLOutput=enabled` mit `QueryAnalysisOutput=enabled` zeigt die Analyse in der Debug-Ausgabe |

## Checkliste

- [ ] Ein unterstütztes MariaDB-Release mit InnoDB.
- [ ] Eine Datenbank pro Website in `utf8mb4`, ein Benutzer mit den Rechten oben.
- [ ] `mysqli` im Web-PHP und im PHP der Kommandozeile.
- [ ] Standardwerte des Panels auf Zeichensatz und Rechte geprüft.
- [ ] Backups mit `mariadb-dump --single-transaction` geplant und eine Wiederherstellung getestet.

## Verwandte Seiten

- [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md) und [MySQL für Hoster](mysql-fuer-hoster.md)
- [Datenbankunterstützung: MariaDB](../../../../en/us/help/database-support.md#4-mariadb) und
  [Zeichensätze](../../../../en/us/help/database-support.md#character-sets-and-collations)
- [Kapitel 9.3: MySQL und MariaDB](../../../../en/us/install/09-databases.md#93-mysql-and-mariadb)
- [Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
