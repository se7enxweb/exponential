---
title: PHP-Erweiterungen je Engine
audience: dba
category: database-administration
order: 5
summary: Welche PHP-Erweiterung jede Datenbank braucht (sqlite3, pgsql, mongodb mit seiner Composer-Bibliothek, mysqli, oci8), woher sie auf den gängigen Linux-Familien kommt, wie der Einrichtungsassistent sie prüft und wie Sie bestätigen, dass das PHP des Webservers, das PHP von Velocity und das PHP der Kommandozeile sie alle haben.
keywords: PHP-Erweiterung, sqlite3, pgsql, mysqli, mysqlnd, mongodb, mongodb/mongodb, oci8, Instant Client, pecl, php -m, php --ri, database_extensions, setup.ini, PHP-FPM, CLI, PHP extension
type: help
---
# PHP-Erweiterungen je Engine

Diese Seite richtet sich an Datenbankadministratoren, die vor einer Exponential-Installation einen Server vorbereiten oder
den PHP-Build eines Hosters prüfen. Sie beantwortet: welche PHP-Erweiterung jede Engine braucht, woher Sie sie bekommen,
wie die Installer sie prüfen und wie Sie sicherstellen, dass jedes PHP, das die Datenbank berührt, sie hat. Quelle:
[Datenbankunterstützung, PHP-Erweiterungen](../datenbankunterstuetzung.md#php-erweiterungen) und `settings/setup.ini`.

## Inhalt

1. [Eine Erweiterung je Engine](#eine-erweiterung-je-engine)
2. [Wie der Einrichtungsassistent prüft](#wie-der-einrichtungsassistent-prüft)
3. [Mehrere PHPs auf einem Server](#mehrere-phps-auf-einem-server)
4. [MongoDB: Erweiterung und Bibliothek](#mongodb-erweiterung-und-bibliothek)
5. [Oracle: oci8 und der Client](#oracle-oci8-und-der-client)
6. [Checkliste](#checkliste)

## Eine Erweiterung je Engine

| Engine | Erweiterung | Woher sie kommt |
|---|---|---|
| SQLite 3 | `sqlite3` | In PHP enthalten; RHEL-Familie `php-pdo` (enthält auch `pdo_sqlite`), Debian und Ubuntu `php<version>-sqlite3`. `pdo_sqlite` braucht der Kernel nicht. |
| PostgreSQL | `pgsql` | In PHP enthalten; `php-pgsql`, `php<version>-pgsql` |
| MongoDB | `mongodb` (PECL) und die Composer-Bibliothek `mongodb/mongodb` | `pecl install mongodb` oder `php-pecl-mongodb`; die Bibliothek mit `composer require mongodb/mongodb` |
| MariaDB, MySQL | `mysqli` | In PHP enthalten (mysqlnd); `php-mysqlnd`, `php<version>-mysql` |
| Oracle | `oci8` (PECL) | PECL oci8 3.4 für PHP 8.4 und 8.5, 3.2 für PHP 8.1 bis 8.3, gegen einen Oracle-Client gebaut; EPEL, Remi oder Ihr Anbieter |

Installieren Sie nur die, die Ihre Website verwendet. Jeder Treiber bricht früh ab, wenn seine Erweiterung fehlt; der
SQLite-Treiber zum Beispiel protokolliert "SQLite3 extension was not found, the DB handler will not be initialized."
([9.2](../../installation/09-datenbanken.md#was-der-treiber-bei-jeder-verbindung-tut)).

## Wie der Einrichtungsassistent prüft

`settings/setup.ini` definiert einen kritischen Test:

```ini
[database_extensions]
Extensions=sqlite3;mysqli;pgsql;mongodb;oci8
Require=one
```

Der Test besteht, wenn mindestens eine davon geladen ist. Der optionale Test `database_all_extensions` listet die
fehlenden; er dient nur der Information. Fehlt `sqlite3`, wählt der Assistent die erste Datenbank, deren Erweiterung
geladen ist, und sagt das. Oracle bietet der Assistent nur an, wenn `oci8` geladen und `extension/ezoracle` vorhanden ist.

## Mehrere PHPs auf einem Server

Das PHP, das die Website ausliefert, und das PHP auf der Kommandozeile werden getrennt konfiguriert, und Velocity kann noch
ein weiteres Binary ausführen. Prüfen Sie jedes:

```bash
php -m | grep -iE 'sqlite3|pgsql|mysqli|mongodb|oci8'      # the command line PHP
php --ri sqlite3 | grep -i library                           # the SQLite library version (3.33 or later)
```

Für das PHP des Webservers öffnen Sie **Setup > Systeminformationen**: Die Karte PHP nennt die SAPI, und die Karte
Datenbank zeigt die Treiberklasse, die geantwortet hat ([Systeminformationen](../../anleitungen/systeminformationen.md)).
Dieselbe Seite, über den Port von Velocity geöffnet, meldet das PHP von Velocity. Auf einem Plesk- oder cPanel-Server hat
jede PHP-Version ihre eigene `php.ini` und ihre eigene Modulliste; siehe
[PHP-Auswahl und FPM-Pools](../hosting/control-panels/php-auswahl-und-fpm-pools.md).

Cronjobs und Konsolenbefehle verwenden das PHP der Kommandozeile. Eine Website, deren Seiten funktionieren, deren Cronjobs
aber mit fehlendem Treiber scheitern, hat die Erweiterung meist nur in PHP-FPM.

## MongoDB: Erweiterung und Bibliothek

Der Adapter `expMongoDB` verwendet die PECL-Erweiterung `mongodb` und den `MongoDB\Client` der Bibliothek. `composer.json`
schlägt die Bibliothek nur vor, eine einfache Installation hat sie also nicht. Ohne sie lautet der Fehler
`Class "MongoDB\Client" not found` ([12.10](../../installation/12-fehlerbehebung.md#1210-datenbanken)). Anforderungen:

- PHP 8.2 oder neuer (der einzige Treiber mit einer höheren Untergrenze als PHP 8.0).
- Erweiterung `mongodb` 1.18 oder neuer.
- Bibliothek `mongodb/mongodb`; die CI installiert `^1.21 || ^2.0`.

## Oracle: oci8 und der Client

`oci8` muss gegen einen Oracle-Client gebaut sein. Der Instant Client genügt, und `ORACLE_HOME` wird damit nicht gebraucht.
Setzen Sie `LD_LIBRARY_PATH` (und `TNS_ADMIN` für TNS-Aliase) nur, wenn der Client außerhalb des Bibliothekspfads des
Systems liegt, und dann auch im PHP-FPM-Pool (`env[...]`) und in der Umgebung von Velocity, weil jeder Prozess sie einmal
liest ([9.6](../../installation/09-datenbanken.md#96-oracle-19c-und-neuer-ezoracle)). Die `php.ini`-Einstellungen
`oci8.persistent_timeout`, `oci8.max_persistent` und `oci8.ping_interval` steuern persistente Verbindungen und lassen
sich zur Laufzeit nicht ändern ([Oracle: persistente Verbindungen, DRCP und Tuning](oracle-verbindungen-und-tuning.md)).

## Checkliste

- [ ] Die Erweiterung der gewählten Engine ist im PHP des Webservers geladen.
- [ ] Dieselbe Erweiterung ist im PHP der Kommandozeile geladen (Cronjobs, Konsole).
- [ ] Unter Velocity ist dieselbe Erweiterung im PHP von Velocity geladen.
- [ ] SQLite: Die Bibliothek in PHP ist 3.33 oder neuer.
- [ ] MongoDB: PHP 8.2+, Erweiterung 1.18+, Bibliothek `mongodb/mongodb` installiert.
- [ ] Oracle: `oci8` passt zur PHP-Version; die Client-Bibliotheken werden von jedem Prozess gefunden.
- [ ] Erweiterungen für Engines, die die Website nicht verwendet, sind nicht installiert.

## Verwandte Seiten

- [Unterstützte Engines und Versionen](unterstuetzte-engines-und-versionen.md)
- [PHP-Versionen und -Erweiterungen](../hosting/grundlagen/php-versionen-und-erweiterungen.md) (für Hosting-Unternehmen)
- [Kapitel 2, Anforderungen](../../installation/02-voraussetzungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
