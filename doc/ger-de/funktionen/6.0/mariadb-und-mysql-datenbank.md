# Unterstützung für die Datenbanken MariaDB und MySQL

Diese Seite ist für Administratoren und Datenbankadministratoren, die eine Exponential-Website auf MariaDB oder MySQL
betreiben. Beide laufen auf demselben Treiber und teilen sich deshalb eine Seite: die Unterstützungsstufe, was Server
und PHP brauchen, die Einstellungen, eine Installation, die Grenzen und die getesteten Versionen. Die ausführlicheren
Kapitel sind dort verlinkt, wo sie mehr sagen.

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Empfehlung:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server und Azure SQL (weitere
> Datenbanken ab 6.0.15: [Eine Datenbank wählen](../../hilfe/datenbank-waehlen.md)). Einzelheiten:
> [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md#unterstützungsstufen-auf-einen-blick).

MariaDB ist der MySQL-kompatible Server, den das Projekt bevorzugt; MySQL steht in der Reihenfolge zuletzt, nicht weil
es schlechter funktioniert, sondern weil das Projekt für neue Websites zuerst die anderen empfiehlt. Im
Setup-Assistenten sind beide ein Eintrag, **MariaDB / MySQL Improved**.

## Inhalt

1. [Unterstützungsstufe](#unterstützungsstufe)
2. [Voraussetzungen](#voraussetzungen)
3. [PHP-Erweiterung](#php-erweiterung)
4. [Einstellungen](#einstellungen)
5. [Eine Website installieren](#eine-website-installieren)
6. [Grenzen](#grenzen)
7. [Getestete Versionen](#getestete-versionen)
8. [Datenbanken, die das Protokoll von MySQL sprechen](#datenbanken-die-das-protokoll-von-mysql-sprechen)
9. [Verwandte Seiten](#verwandte-seiten)

## Unterstützungsstufe

| | Datentabellen | Ganze Website | DFS | Cluster mit mehreren Servern | Setup-Assistent | `exp:install` / Kickstarter | xne-Upgrade getestet |
|---|---|---|---|---|---|---|---|
| MariaDB | ja (`Engine=mysql`) | ja | ja, im Kernel (das Standard-Backend) | ja, mit Lesereplikaten | ja, als MariaDB / MySQL Improved (`mysqli`) | `--db=mariadb` | ja: 6.0.14 auf 6.0.15 und ein exaktes Zurückrollen, auch eines fehlgeschlagenen Schritts |
| MySQL | ja (`Engine=mysql`) | ja | ja, im Kernel (das Standard-Backend) | ja, mit Lesereplikaten | ja, als MariaDB / MySQL Improved (`mysqli`) | `--db=mysql` | derselbe Treiber und Weg, getestet auf MariaDB |

## Voraussetzungen

- **Ein Release, das Ihr Hersteller noch pflegt**, mit InnoDB (Transaktionen brauchen es) und dem Zeichensatz
  `utf8mb4`.
- **Eine Datenbank und ein Benutzer** mit Rechten nur auf diese Datenbank; ein globales Recht ist nicht nötig:

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<ein starkes Passwort>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

Die Vorbereitung Schritt für Schritt steht unter
[MySQL und MariaDB einrichten](../../hilfe/datenbankadministratoren/mysql-und-mariadb-einrichten.md).

## PHP-Erweiterung

`mysqli`, mit PHP über mysqlnd ausgeliefert (`php-mysqlnd`, `php<version>-mysql`). Der Setup-Assistent bietet MariaDB /
MySQL Improved und die Datenbanken des MySQL-Protokolls an, sobald das PHP, das ihn ausführt, `mysqli` hat. Eine
Datentabellen-Verbindung oder eine Importquelle über PDO nutzt `pdo_mysql`.

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
# leer: 3306
Port=
User=exponential
Password=<das Passwort>
Database=exponential
# ein Unix-Socket statt Host und Port
Socket=disabled
Charset=utf-8
UsePersistentConnection=disabled
```

`ezmysqli`, `mysqli`, `ezmysql` und `mysql` nennen alle den Treiber `eZMySQLiDB`. Der Block gehört in
`settings/override/site.ini.append.php` (die Installer schreiben ihn für Sie); danach den INI-Cache leeren.
Lesereplikate: `UseSlaveServer=enabled` mit `SlaveServerArray[]`; nur einfache SELECTs anonymer GET- und
HEAD-Anfragen gehen an ein Replikat, und nur an eines, das aktuell ist
([MySQL-Lesereplikate](../../hilfe/datenbankadministratoren/mysql-lesereplikate.md)).

## Eine Website installieren

```bash
export EXP_INSTALL_DB_PASSWORD='<das Passwort>'
php bin/php/console exp:install --db=mariadb --db-host=localhost --db-user=exponential --db-name=exponential
```

`--db=mariadb` und `--db=mysql` sind dasselbe. Im Browser wählen Sie auf der Datenbankseite des Setup-Assistenten
**MariaDB / MySQL Improved**; der Kickstarter nimmt `Type=mysqli` in `[database_choice]`
([Installation über die Konsole](../../installation/07-installation-ueber-die-konsole.md)).

## Grenzen

- Der Verbindungszeichensatz `utf-8` wird zum `utf8` des Servers, also zum Drei-Byte-`utf8mb3`: Zeichen außerhalb der
  Basic Multilingual Plane, etwa Emoji, kommen nicht durch die Verbindung, auch wenn die Tabellen `utf8mb4` sind
  ([Zeichensatz und InnoDB](../../hilfe/datenbankadministratoren/mysql-und-mariadb-zeichensatz-und-innodb.md)).
- Tabellen einer alten Installation, die noch MyISAM sind, haben keine Transaktionen:
  `php bin/php/ezconvertmysqltabletype.php --list` listet sie, `--newtype=InnoDB` wandelt sie um.
- MySQL und MariaDB sortieren NULL aufsteigend zuerst, wo PostgreSQL und Oracle es zuletzt sortieren.

## Getestete Versionen

MySQL 8.0 in der CI bei jedem Push; MariaDB über denselben Treiber, und MariaDB 10.5 auf dem Entwicklungsserver. Die
Integrationssuite von xne aktualisiert eine echte 6.0.14-Website auf MariaDB auf den aktuellen Code und rollt sie exakt
zurück, auch nach einem Datenbankschritt, der absichtlich scheitert.

## Datenbanken, die das Protokoll von MySQL sprechen

TiDB, Vitess und PlanetScale, SingleStore, MariaDB Xpand und Amazon Aurora MySQL installieren eine ganze Website auf
derselben Erweiterung, mit je einem Profil:
[Datenbanken, die das MySQL-Protokoll sprechen](mysql-wire-datenbanken.md). Verwaltete MariaDB- und MySQL-Dienste wie
Amazon RDS sind für den Treiber einfaches MariaDB oder MySQL.

## Verwandte Seiten

- [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md) und [Eine Datenbank wählen](../../hilfe/datenbank-waehlen.md).
- [9.3 MySQL und MariaDB](../../installation/09-datenbanken.md#93-mysql-und-mariadb), das Kapitel für den Betrieb.
- [MySQL und MariaDB einrichten](../../hilfe/datenbankadministratoren/mysql-und-mariadb-einrichten.md),
  [MySQL-Lesereplikate](../../hilfe/datenbankadministratoren/mysql-lesereplikate.md).
- [Clustering in Exponential 6.0](clustering-ueberblick.md).
