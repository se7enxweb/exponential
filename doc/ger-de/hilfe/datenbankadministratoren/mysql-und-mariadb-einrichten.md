---
title: MySQL und MariaDB einrichten
audience: dba
category: database-administration
order: 14
summary: Wie Sie MySQL oder MariaDB für eine Exponential-Website vorbereiten - eine utf8mb4-Datenbank, ein Konto nur mit Rechten auf diese Datenbank, der Einstellungsblock mit Host, Port oder Socket, die Installation über die Konsole und was sich für Exponential zwischen MariaDB und MySQL unterscheidet.
keywords: MySQL, MariaDB, CREATE DATABASE, utf8mb4, utf8mb4_unicode_ci, CREATE USER, GRANT, LOCK TABLES, CREATE TEMPORARY TABLES, InnoDB, ezmysqli, mysqli, Socket, exp:install --db=mysql, --db=mariadb, MySQL Improved, Einrichtung, Rechte, setup, privileges
type: help
---
# MySQL und MariaDB einrichten

Diese Seite richtet sich an Datenbankadministratoren, die MySQL oder MariaDB für eine Exponential-Website vorbereiten. Beide
laufen über denselben Treiber, `eZMySQLiDB` (`lib/ezdb/classes/ezmysqlidb.php`), mit der PHP-Erweiterung `mysqli`. Die
Seite beantwortet: wie Sie die Datenbank und das Konto anlegen, welche Rechte gebraucht werden und warum, wie die
Einstellungen aussehen und wie Sie den Installer ausführen. Quelle:
[9.3 MySQL und MariaDB](../../installation/09-datenbanken.md#93-mysql-und-mariadb).

## Inhalt

1. [Anforderungen](#anforderungen)
2. [Die Datenbank und das Konto anlegen](#die-datenbank-und-das-konto-anlegen)
3. [Warum diese Rechte](#warum-diese-rechte)
4. [Die Einstellungen](#die-einstellungen)
5. [Installieren](#installieren)
6. [MariaDB und MySQL: was sich unterscheidet](#mariadb-und-mysql-was-sich-unterscheidet)
7. [Erste Prüfungen](#erste-prüfungen)
8. [Checkliste](#checkliste)

## Anforderungen

- Ein MySQL- oder MariaDB-Release, das Ihr Hersteller noch mit Sicherheitskorrekturen versorgt, mit InnoDB und `utf8mb4`.
  Die Untergrenze 4.1.1 des Assistenten ist historisch. Die CI des Projekts verwendet MySQL 8.0; der Entwicklungsserver
  MariaDB 10.5.
- Die PHP-Erweiterung `mysqli` (mysqlnd) in jedem PHP, das die Website verwendet.

## Die Datenbank und das Konto anlegen

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

Verwenden Sie statt `localhost` den Host oder das Netz der Webserver, wenn die Datenbank auf einer anderen Maschine liegt.
Keiner der Installer legt die Datenbank oder das Konto an.

## Warum diese Rechte

- `SELECT`, `INSERT`, `UPDATE`, `DELETE`: die normale Arbeit der Website.
- `CREATE`, `DROP`, `INDEX`, `ALTER`: Der Installer und jede spätere Update-Datei legen Tabellen und Indizes an und ändern sie.
- `LOCK TABLES`: `lock()` des Treibers verwendet `LOCK TABLES`.
- `CREATE TEMPORARY TABLES`: Der Kernel legt für einige Operationen temporäre Tabellen an (Namen wie `ezsearch_tmp_12`).

Kein globales Recht, kein `SUPER` und kein Recht, Datenbanken aufzulisten, wird gebraucht: Der Assistent arbeitet mit
Rechten je Datenbank. Trennt Ihre Richtlinie ein Migrationskonto vom Laufzeitkonto, braucht das Laufzeitkonto trotzdem
`CREATE TEMPORARY TABLES` und `LOCK TABLES`; testen Sie die Trennung auf Staging, weil das Projekt eine solche Trennung
weder dokumentiert noch testet ([Datenbankkonten und Rechte](datenbankkonten-und-rechte.md)).

## Die Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
# empty: 3306
Port=
User=exponential
Password=<the password>
Database=exponential
# a Unix socket instead of host and port, e.g. /var/lib/mysql/mysql.sock
Socket=disabled
Charset=utf-8
# enabled: mysqli persistent connections (the driver prefixes the host with p:)
UsePersistentConnection=disabled
ConnectRetries=0
```

`ezmysqli`, `mysqli`, `ezmysql` und `mysql` nennen alle diesen Treiber.

## Installieren

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --apply --db=mysql \
    --db-host=127.0.0.1 --db-name=exponential --db-user=exponential --allow-root-user
```

`--db=mariadb` ist dasselbe. Wählen Sie im Einrichtungsassistenten "MySQL Improved"; er prüft, dass der
Standardzeichensatz der Datenbank UTF-8 ist, und lehnt eine `latin1`-Datenbank ab. Der Kickstarter nimmt
`[database_choice] Type=mysqli` (`mysql` wird akzeptiert). Der Installer legt InnoDB-Tabellen an, wenn der Server InnoDB
anbietet (`kernel/setup/steps/ezstep_create_sites.php`).

## MariaDB und MySQL: was sich unterscheidet

| | MariaDB | MySQL |
|---|---|---|
| Platz in der offiziellen Reihenfolge | 4 | 6 |
| In der CI des Projekts | nein (indirekt durch den MySQL-Job abgedeckt) | ja, MySQL 8.0 |
| Dump-Werkzeug | `mariadb-dump` (oder der ältere Name `mysqldump`) | `mysqldump` |
| Verbindungsname `utf8` | bedeutet `utf8mb3` | bedeutet `utf8mb3` |
| `SET storage_engine` in alten Update-Dateien | in 12.0 entfernt | in 5.7.5 entfernt |

Für Exponential gibt es keinen weiteren Unterschied: derselbe Treiber, dieselben Einstellungen, dieselben Update-Dateien in
`update/database/mysql/`.

## Erste Prüfungen

```bash
mysql -u exponential -p exponential -e "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
mysql -u exponential -p exponential -e "SELECT ENGINE, COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='exponential' GROUP BY ENGINE;"
```

Alle Tabellen sollten InnoDB sein. Öffnen Sie dann **Setup > Systeminformationen**: Die Karte Datenbank zeigt die Engine,
die Serverversion und den Zeichensatz.

## Checkliste

- [ ] Ein unterstütztes MySQL- oder MariaDB-Release mit InnoDB.
- [ ] Die Datenbank ist `utf8mb4`.
- [ ] Das Konto hat die obigen Rechte nur auf diese Datenbank und ist nicht `root`.
- [ ] Das Konto ist auf die Hosts der Webserver beschränkt.
- [ ] Sicherungen mit `--single-transaction` sind eingeplant ([Sicherungen je Engine](sicherungen-je-engine.md)).
- [ ] `AlwaysLog[]=warning` war eingeschaltet, während Sie die Website eingerichtet haben, um Zeichensatzwarnungen zu sehen.

## Verwandte Seiten

- [MySQL und MariaDB: utf8mb4, der Verbindungszeichensatz und InnoDB](mysql-und-mariadb-zeichensatz-und-innodb.md)
- [Lesereplikate für MySQL und MariaDB](mysql-lesereplikate.md)
- [MySQL für Hoster](../hosting/grundlagen/mysql-fuer-hoster.md), [MariaDB für Hoster](../hosting/grundlagen/mariadb-fuer-hoster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
