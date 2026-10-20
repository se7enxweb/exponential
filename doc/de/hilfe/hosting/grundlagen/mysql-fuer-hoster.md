---
title: MySQL für Hoster
audience: hosting, admin
category: hosting
order: 120
summary: MySQL als Datenbank einer Exponential-Website aus Sicht eines Hosters - wo es in der offiziellen Reihenfolge steht, Versionen, die Rechte, die der Benutzer einer Website braucht, InnoDB und utf8mb4, Einstellungen, Lese-Replikate, Backups und die üblichen Probleme.
keywords: MySQL, mysqli, ezmysqli, MySQL 8.0, utf8mb4, InnoDB, Rechte, CREATE USER, mysqldump, single-transaction, UseSlaveServer, SlaveServerArray, Replikate, persistente Verbindungen, QueryAnalysisOutput, Hosting, grants, replicas, persistent connections, hosting
type: help
---
# MySQL für Hoster

Diese Seite ist für Systemadministratoren bei einem Hosting-Anbieter, die MySQL-Datenbanken für Exponential-Websites
bereitstellen. Sie beantwortet: wo MySQL steht, wie Sie die Datenbank und den Benutzer einer Website anlegen, was der
Treiber vom Server braucht, wie Sie Replikate nutzen und wie Sie sichern. MySQL ist die sechste in der offiziellen
Reihenfolge: unterstützt und in der CI des Projekts getestet, nur deshalb zuletzt, weil das Projekt für neue Websites
zuerst die anderen empfiehlt. Quellen: [Datenbankunterstützung](../../../../en/us/help/database-support.md#6-mysql) und
[Kapitel 9.3](../../../../en/us/install/09-databases.md#93-mysql-and-mariadb).

## Inhalt

1. [Wo MySQL steht](#wo-mysql-steht)
2. [Versionen und Erweiterung](#versionen-und-erweiterung)
3. [Eine Datenbank pro Website anlegen](#eine-datenbank-pro-website-anlegen)
4. [Einstellungen und Installation](#einstellungen-und-installation)
5. [Replikate und persistente Verbindungen](#replikate-und-persistente-verbindungen)
6. [Backups](#backups)
7. [Probleme und ihre Ursachen](#probleme-und-ihre-ursachen)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Wo MySQL steht

- Der Treiber `eZMySQLiDB` auf `mysqli` ist der, für den die meisten Erweiterungen von Drittanbietern ursprünglich
  geschrieben wurden; MySQL ist daher die sichere Wahl für einen Kunden mit älteren Erweiterungen, die eigenes SQL
  enthalten.
- Es teilt den Treiber mit MariaDB ([MariaDB für Hoster](mariadb-fuer-hoster.md)); für einen Hoster werden beide gleich
  bereitgestellt.
- Die CI des Projekts führt ihre Datenbanktests bei jedem Push gegen MySQL 8.0 aus
  ([Testabdeckung](../../../../en/us/help/database-support.md#test-coverage-ci)).

## Versionen und Erweiterung

| Element | Wert |
|---|---|
| Untergrenze des Assistenten | 4.1.1 (keine Empfehlung) |
| Praktisches Minimum | ein Release, das Ihr Hersteller noch unterstützt, mit `utf8mb4` und InnoDB |
| Funktionierend gesehen | 8.0 in der CI |
| PHP-Erweiterung | `mysqli` (`php-mysqlnd` in der RHEL-Familie, `php<version>-mysql` unter Debian und Ubuntu) |
| Namen in den Installern | Assistent „MySQL Improved“; `exp:install --db=mysql` (auch `mysqli`); Kickstarter `Type=mysqli` |

## Eine Datenbank pro Website anlegen

```sql
CREATE DATABASE <customer> CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '<customer>'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON <customer>.* TO '<customer>'@'localhost';
```

Es ist kein globales Recht nötig. Der Installer legt InnoDB-Tabellen an, wenn der Server InnoDB anbietet, und
Transaktionen brauchen es. Die MySQL-Update-Dateien eines Upgrades beginnen mit `SET default_storage_engine=InnoDB;`
([Kapitel 9.3](../../../../en/us/install/09-databases.md#character-set-and-storage-engine)).

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
# a Unix socket instead of host and port, e.g. /var/lib/mysql/mysql.sock
Socket=disabled
Charset=utf-8
UsePersistentConnection=disabled
ConnectRetries=0
```

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=mysql \
    --db-host=127.0.0.1 --db-name=<customer> --db-user=<customer> --allow-root-user
```

> **Hinweis**
> Der Verbindungszeichensatz `utf-8` wird als MySQLs `utf8` gesendet, drei Byte pro Zeichen; Zeichen außerhalb der
> Basic Multilingual Plane, etwa Emoji, können daher nicht durch die Verbindung gehen, auch wenn die Tabellen `utf8mb4`
> sind. Kunden, die sie brauchen, sollten das wissen, bevor sie MySQL oder MariaDB wählen
> ([Zeichensätze](../../../../en/us/help/database-support.md#character-sets-and-collations)).

## Replikate und persistente Verbindungen

- **Lese-Replikate.** `UseSlaveServer=enabled` mit `SlaveServerArray[]`, `SlaveServerPort[]`, `SlaverServerUser[]`,
  `SlaverServerPassword[]` und `SlaverServerDatabase[]` (die Schlüssel heißen wirklich `Slaver...`) schickt Lesezugriffe
  an ein Replikat, das pro Anfrage zufällig gewählt wird; Schreibzugriffe gehen an `Server`. Durch die
  Replikationsverzögerung kann eine Seite Inhalte zeigen, die einige Sekunden alt sind.
- **Persistente Verbindungen.** `UsePersistentConnection=enabled` hält eine Verbindung pro PHP-Prozess (der Treiber
  stellt dem Host `p:` voran). Unter den persistenten Workern von Velocity hält jeder Worker seine Verbindung ohnehin.
  Bemessen Sie das Verbindungslimit des Servers für die Anzahl der PHP-Prozesse aller Websites, die ihn nutzen.

## Backups

```bash
mysqldump --single-transaction -u <customer> -p <customer> > <customer>.sql
mysql -u <customer> -p <customer> < <customer>.sql
```

`--single-transaction` ergibt einen konsistenten Dump von InnoDB-Tabellen, ohne sie zu sperren. Ein vollständiges Backup
enthält außerdem `var/storage`, `settings/override` und `settings/siteaccess`
([Backup und Wiederherstellung](../../../../en/us/help/database-support.md#backup-and-restore)).

## Probleme und ihre Ursachen

| Symptom | Ursache |
|---|---|
| Die Fehlerseite, dass die Datenbank nicht erreichbar ist | falscher Host, Benutzer, falsches Passwort oder falsche Datenbank; seit PHP 8.1 wirft `mysqli` eine Ausnahme, und der Treiber macht daraus `eZDBNoConnectionException` |
| Text mit `?` an der Stelle von Buchstaben mit Akzent | die Verbindung ist auf den Standardzeichensatz des Servers zurückgefallen; setzen Sie `AlwaysLog[]=warning` und suchen Sie in `var/log/warning.log` nach „could not set the connection charset“ |
| Der Assistent lehnt den Zeichensatz der Datenbank ab | die Datenbank ist nicht UTF-8; legen Sie sie in `utf8mb4` neu an |
| Langsame Abfragen | `[DatabaseSettings] QueryAnalysisOutput=enabled` mit `SQLOutput=enabled` zeigt die Analyse von MySQL in der Debug-Ausgabe |

## Checkliste

- [ ] Ein unterstütztes MySQL-Release mit InnoDB.
- [ ] Eine Datenbank pro Website in `utf8mb4`, ein Benutzer mit den Rechten oben.
- [ ] `mysqli` im Web-PHP und im PHP der Kommandozeile.
- [ ] Das Verbindungslimit des Servers für die PHP-Prozesse aller Websites auf dem Server bemessen.
- [ ] Backups mit `mysqldump --single-transaction` geplant und eine Wiederherstellung getestet.

## Verwandte Seiten

- [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md) und [MariaDB für Hoster](mariadb-fuer-hoster.md)
- [Datenbankunterstützung: MySQL](../../../../en/us/help/database-support.md#6-mysql)
- [Kapitel 9.3: MySQL und MariaDB](../../../../en/us/install/09-databases.md#93-mysql-and-mariadb)
- [Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
