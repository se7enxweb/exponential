---
title: Datenbanken in der offiziellen Reihenfolge
audience: hosting, admin
category: hosting
order: 60
summary: Die sechs Datenbanken, die Exponential 6.0.x unterstützt, in der offiziellen Reihenfolge des Projekts (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), für Hoster nebeneinander verglichen, mit der Wahl, welche Sie anbieten, und was jede im Betrieb kostet. Microsoft SQL Server ist bekanntermaßen mit zusätzlicher Arbeit möglich.
keywords: Datenbank, Datenbanken, offizielle Reihenfolge, unterstützte Datenbanken, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, DatabaseImplementation, mysqli, pgsql, sqlite3, oci8, Hosting-Tarife, Datenbankangebot, verwaltete Datenbank, CI, database, databases, official order, supported databases, hosting plans, database offer, managed database
featured: true
type: help
---
# Datenbanken in der offiziellen Reihenfolge

Diese Seite ist für Produktmanager, die entscheiden, welche Datenbanken ein Hosting-Angebot enthält, und für
Systemadministratoren, die sie betreiben werden. Sie beantwortet: Welche Datenbanken Exponential 6.0.x unterstützt, in
welcher Reihenfolge das Projekt sie empfiehlt, wie sie sich aus Sicht eines Hosters vergleichen und wie Sie wählen, was
Sie anbieten. Die Aussage zur Unterstützung und jedes Detail stehen in [Datenbankunterstützung](../../datenbankunterstuetzung.md);
das Kapitel für den Betrieb ist [Kapitel 9, Datenbanken](../../../installation/09-datenbanken.md).

## Inhalt

1. [Die offizielle Reihenfolge](#die-offizielle-reihenfolge)
2. [Nebeneinander](#nebeneinander)
3. [Was jede für einen Hoster bedeutet](#was-jede-für-einen-hoster-bedeutet)
4. [Wählen, was Sie anbieten](#wählen-was-sie-anbieten)
5. [Was alle Datenbanken gemeinsam haben](#was-alle-datenbanken-gemeinsam-haben)
6. [Checkliste](#checkliste)
7. [Verwandte Seiten](#verwandte-seiten)

## Die offizielle Reihenfolge

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlicher Arbeit möglich.

Alle sechs haben einen Treiber im Kernel oder in einer mitgelieferten Erweiterung, einen Eintrag im Setup-Assistenten
und einen Weg in den Installern für die Kommandozeile ([Datenbankunterstützung](../../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)).
Die Reihenfolge ist die Reihenfolge der Empfehlung für neue Websites; sie bedeutet nicht, dass die späteren weniger gut
funktionieren. Microsoft SQL Server hat in 6.0.x keinen Treiber ([Stand von Microsoft SQL Server](stand-von-microsoft-sql-server.md)).

Verwenden Sie diese Reihenfolge überall, wo Ihr eigenes Material Datenbanken aufführt: Tarifbeschreibungen,
Bestellformulare, Support-Artikel.

## Nebeneinander

| | 1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL |
|---|---|---|---|---|---|---|
| Zu betreibender Datenbankserver | keiner (eine Datei) | ja | ja | ja | ja | ja |
| `DatabaseImplementation` | `sqlite3` | `ezpostgresql` | `mongodb` | `ezmysqli` | `ezoracle` | `ezmysqli` |
| PHP-Erweiterung | `sqlite3` | `pgsql` | `mongodb` + Bibliothek `mongodb/mongodb` | `mysqli` | `oci8` | `mysqli` |
| `exp:install --db=` | `sqlite` (Standard) | `pgsql` | `mongodb` | `mariadb` | `oracle` | `mysql` |
| Mehrere Webserver an einer Datenbank | nein | ja | ja | ja | ja | ja |
| Praktisches Minimum | Bibliothek 3.33 | 13 | Server 6.0, PHP 8.2 | ein vom Hersteller unterstütztes Release | 19c | ein vom Hersteller unterstütztes Release |
| Zeichensatz | UTF-8 | `UTF8` | UTF-8 (BSON) | `utf8mb4` | `AL32UTF8` | `utf8mb4` |
| In der CI des Projekts getestet | Unit-Tests des Treibers | ja (PostgreSQL 15) | Unit-Tests des Adapters | nein (durch den MySQL-Job abgedeckt, derselbe Treiber) | nein | ja (MySQL 8.0) |
| Backup-Werkzeug | `sqlite3 .backup` | `pg_dump` | `mongodump` | `mariadb-dump` | `ext:ezoracle:datapump`, `expdp` | `mysqldump` |

Quellen: [Auf einen Blick](../../datenbankunterstuetzung.md#auf-einen-blick),
[Versionen](../../datenbankunterstuetzung.md#versionen-am-code-überprüft) und
[Testabdeckung](../../datenbankunterstuetzung.md#testabdeckung-ci).

## Was jede für einen Hoster bedeutet

| Datenbank | Für einen Hoster | Seite |
|---|---|---|
| 1. SQLite 3 | Nichts zu betreiben oder abzurechnen: Die Datenbank liegt im eigenen `var/storage/sqlite3/` der Website. Passt zu jedem Tarif, in dem eine Maschine die Website ausliefert, auch zu Shared Hosting. Backups sind eine Dateioperation, aber nie eine einfache Kopie einer Datei in Benutzung. | [SQLite für Hoster](sqlite-fuer-hoster.md) |
| 2. PostgreSQL | Der empfohlene Datenbankserver. Eine Datenbank pro Website, mit dem Benutzer der Website als Eigentümer; `pgcrypto` muss verfügbar sein. | [PostgreSQL für Hoster](postgresql-fuer-hoster.md) |
| 3. MongoDB | Für Kunden, die auf MongoDB standardisiert haben. Braucht PHP 8.2, eine Composer-Bibliothek, einen Benutzer in der Datenbank der Website und eine Reihe von Indizes; hat dokumentierte Grenzen. | [MongoDB für Hoster](mongodb-fuer-hoster.md) |
| 4. MariaDB | Der MySQL-kompatible Server, den das Projekt bevorzugt; die übliche Datenbank von Hosting-Panels. | [MariaDB für Hoster](mariadb-fuer-hoster.md) |
| 5. Oracle | Für Kunden, die Oracle betreiben. Braucht `oci8`, gebaut gegen einen Oracle-Client; meist die eigene Datenbank des Kunden. | [Oracle für Hoster](oracle-fuer-hoster.md) |
| 6. MySQL | Unterstützt und in der CI mit demselben Treiber wie MariaDB getestet; zuletzt in der Reihenfolge, weil das Projekt für neue Websites zuerst die anderen empfiehlt. | [MySQL für Hoster](mysql-fuer-hoster.md) |

## Wählen, was Sie anbieten

Das Projekt schreibt kein Hosting-Angebot vor. Diese Fragen, entnommen der Entscheidungstabelle in
[Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md), entscheiden es:

| Frage | Wenn ja |
|---|---|
| Muss mehr als eine Maschine in die Datenbank der Website schreiben? | Ein Datenbankserver, zuerst PostgreSQL; nicht SQLite |
| Ist der Tarif eine einzelne Maschine, geteilt oder nicht? | SQLite 3 deckt ihn ab, ohne zu betreibenden Datenbankserver |
| Bringen Kunden ihr eigenes MongoDB oder Oracle mit? | Bieten Sie diese Datenbank als Option an, mit den Anforderungen ihrer Seite |
| Stellt Ihr Panel nur Konten für MariaDB oder MySQL bereit? | Bieten Sie MariaDB an, und SQLite, wo das Panel ein beschreibbares Verzeichnis erlaubt |
| Betreiben Sie bereits einen Datenbankdienst? | Bieten Sie die Engine an, die er betreibt, wenn sie zu den sechs gehört |

Typische Angebote, die ein Hoster daraus bauen kann:

- **Einstiegstarife**: nur SQLite 3. Kein Datenbankkonto bereitzustellen, nichts abzustimmen, Backups pro Website.
- **Business-Tarife**: SQLite 3 oder PostgreSQL, mit MariaDB, wo das Panel es bereitstellt.
- **Enterprise- oder dedizierte Tarife**: jede der sechs, einschließlich des eigenen MongoDB oder Oracle des Kunden.

> **Tipp**
> Was immer Sie anbieten: Schreiben Sie die Engine jeder Kunden-Website in Ihre Bestandsliste. Backups,
> Wiederherstellungen, Upgrades und Support unterscheiden sich je Engine, und die Update-Dateien eines Upgrades werden
> nach der Engine gewählt, auf der die Website jetzt läuft ([eine Website umziehen](../../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen)).

## Was alle Datenbanken gemeinsam haben

- **Die Verbindung** wird in `settings/site.ini`, Block `[DatabaseSettings]`, konfiguriert und in
  `settings/override/site.ini.append.php` überschrieben (von den Installern geschrieben). Schreiben Sie nie ein echtes
  Passwort in eine Datei unter Versionsverwaltung.
- **Die Installer legen die Datenbank oder den Benutzer eines Datenbankservers nicht an.** Legen Sie sie zuerst an; jede
  Seite zeigt, wie.
- **`exp:install` liest das Passwort** aus der Umgebungsvariablen `EXP_INSTALL_DB_PASSWORD`, sodass es nicht in der
  Shell-Historie und der Prozessliste landet.
- **Nach einer Änderung der Einstellungen** leeren Sie den INI-Cache und starten die Worker von Velocity neu:

  ```bash
  php bin/php/ezcache.php --clear-tag=ini --allow-root-user
  php bin/php/console exp:velocity restart --allow-root-user
  ```

- **Welche Datenbank nutzt eine Website?**

  ```bash
  php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user
  ```

  oder **Setup > Systeminformationen**, die Karte Datenbank.
- **Ein vollständiges Backup** besteht aus der Datenbank und den Dateien: `var/storage`, `settings/override` und
  `settings/siteaccess` ([Backup und Wiederherstellung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung)).
- **Ein Wechsel zwischen Engines** ist möglich; siehe [Die Datenbank-Engine wechseln](../migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md).

## Checkliste

- [ ] Die Datenbanken des Tarifs sind in der offiziellen Reihenfolge aufgeführt.
- [ ] Die PHP-Erweiterung jeder angebotenen Datenbank ist im Web-PHP und im PHP der Kommandozeile installiert.
- [ ] Es gibt ein Verfahren, um pro Website eine Datenbank und einen Benutzer anzulegen (nur Server).
- [ ] Backups je Engine sind eingerichtet und getestet ([Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)).
- [ ] Die Engine jeder Kunden-Website ist festgehalten.

## Verwandte Seiten

- [Datenbankunterstützung](../../datenbankunterstuetzung.md) und [Kapitel 9, Datenbanken](../../../installation/09-datenbanken.md)
- [SQLite](sqlite-fuer-hoster.md), [PostgreSQL](postgresql-fuer-hoster.md), [MongoDB](mongodb-fuer-hoster.md),
  [MariaDB](mariadb-fuer-hoster.md), [Oracle](oracle-fuer-hoster.md), [MySQL](mysql-fuer-hoster.md) für Hoster
- [Stand von Microsoft SQL Server](stand-von-microsoft-sql-server.md)
- [Datenbanken und Benutzer in Control Panels](../control-panels/datenbanken-und-benutzer.md)
- [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
