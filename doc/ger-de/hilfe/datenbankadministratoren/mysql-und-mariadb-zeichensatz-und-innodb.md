---
title: "MySQL und MariaDB: utf8mb4, der Verbindungszeichensatz und InnoDB"
audience: dba
category: database-administration
order: 15
summary: Die Details zu Zeichensatz und Speicher-Engine von Exponential auf MySQL und MariaDB - warum die Datenbank utf8mb4 sein sollte, während die Verbindung utf8 (utf8mb3) verwendet, was die Zeichensatzwarnung bedeutet und wie Sie sie sehen, alte Datenbanken mit ezconvertdbcharset.php umwandeln und Tabellentypen mit ezconvertmysqltabletype.php prüfen und umwandeln.
keywords: MySQL, MariaDB, utf8mb4, utf8mb3, utf8, Verbindungszeichensatz, Charset, Emoji, could not set the connection charset, AlwaysLog warning, ezconvertdbcharset, ezconvertmysqltabletype, InnoDB, MyISAM, default_storage_engine, latin1, connection charset, emoji
type: help
---
# MySQL und MariaDB: utf8mb4, der Verbindungszeichensatz und InnoDB

Diese Seite richtet sich an Datenbankadministratoren, die Zeichensatz und Tabellentypen einer Exponential-Datenbank auf
MySQL oder MariaDB betreuen, bei einer neuen Installation oder einer alten, weitergeführten Datenbank. Sie beantwortet:
welchen Zeichensatz Sie anlegen, was die Verbindung wirklich verwendet, was passiert, wenn sich der Verbindungszeichensatz
nicht setzen lässt, und wie Sie Tabellentypen und Zeichensätze prüfen und umwandeln. Quellen:
[Zeichensatz und Speicher-Engine](../../installation/09-datenbanken.md#zeichensatz-und-speicher-engine),
`lib/ezdb/classes/ezmysqlcharset.php` und die beiden Umwandlungsskripte.

## Inhalt

1. [Die Datenbank in utf8mb4 anlegen](#die-datenbank-in-utf8mb4-anlegen)
2. [Der Verbindungszeichensatz](#der-verbindungszeichensatz)
3. [Wenn sich der Verbindungszeichensatz nicht setzen lässt](#wenn-sich-der-verbindungszeichensatz-nicht-setzen-lässt)
4. [InnoDB](#innodb)
5. [Eine alte Datenbank nach UTF-8 umwandeln](#eine-alte-datenbank-nach-utf-8-umwandeln)
6. [Checkliste](#checkliste)

## Die Datenbank in utf8mb4 anlegen

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Der Einrichtungsassistent prüft den Standardzeichensatz der Datenbank und akzeptiert `utf8mb4` als UTF-8
(`kernel/setup/steps/ezstep_installer.php`); er lehnt eine Datenbank in `latin1` oder einem anderen Einzelbyte-Zeichensatz
ab ("cannot be used, the setup wizard wants to create the site in ..."). Der Installer legt seine Tabellen ohne eigenen
Zeichensatz an, sie übernehmen also den Standard der Datenbank.

## Der Verbindungszeichensatz

`[DatabaseSettings] Charset=utf-8` (der Standard) wird für die Verbindung auf MySQLs `utf8` abgebildet. MySQL und MariaDB
behandeln diesen Namen als `utf8mb3`, drei Bytes je Zeichen, Zeichen außerhalb der Basic Multilingual Plane (Emoji, einige
CJK-Erweiterungen) können die Verbindung also nicht passieren, selbst wenn die Tabellen `utf8mb4` sind. Halten Sie die
Datenbank trotzdem in `utf8mb4`: Es kostet nichts und ist für eine Änderung der Verbindung bereit. Das Projekt dokumentiert
dies als bekannte Lücke der Engine ([9.7 Vergleich](../../installation/09-datenbanken.md#97-vergleich)); in 6.0.x gibt es keine
Einstellung, die die Verbindung auf `utf8mb4` umstellt.

## Wenn sich der Verbindungszeichensatz nicht setzen lässt

Der Treiber fragt die Verbindung, welchen Zeichensatz sie wirklich hat, versucht es noch einmal und hält, wenn er dann noch
abweicht, fest:

```text
Connection warning: could not set the connection charset to 'utf8'; it is '...'. Text will be transliterated to that character set on its way out of the database.
```

Die Website läuft weiter, und Text kommt mit `?` anstelle der Zeichen zurück, die dem anderen Zeichensatz fehlen. Es ist eine
Warnung, kein Fehler: Sie wird nur nach `var/log/warning.log` geschrieben, wenn Warnungen protokolliert werden. Schalten Sie
das ein, während Sie eine MySQL-Website einrichten:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
AlwaysLog[]=warning
```

Laden Sie dann eine Seite und durchsuchen Sie `var/log/warning.log` nach "could not set the connection charset".
**Setup > Systeminformationen** warnt ebenfalls, wenn der Zeichensatz der Verbindung nicht UTF-8 ist.

## InnoDB

Transaktionen brauchen InnoDB, und der Installer legt InnoDB-Tabellen an, wenn der Server es anbietet. Eine ältere
Installation kann noch MyISAM-Tabellen haben:

```bash
php bin/php/ezconvertmysqltabletype.php --list --allow-root-user
php bin/php/ezconvertmysqltabletype.php --newtype=InnoDB --allow-root-user
```

Der Konsolenname ist `exp:ezconvertmysqltabletype`. Das Skript verweigert die Ausführung auf jeder Engine außer MySQL und
MariaDB (`kernel/private/classes/commands/ezconvertmysqltabletype.php`). Erstellen Sie zuerst eine Sicherung; die
Umwandlung einer großen Tabelle baut sie neu auf.

Die MySQL-Update-Dateien eines Upgrades beginnen mit `SET default_storage_engine=InnoDB;`, was jedes aktuelle MySQL und
MariaDB akzeptiert. Kopien von vor Oktober 2026 enthielten `SET storage_engine=InnoDB;`, eine Schreibweise, die MySQL in
5.7.5 und MariaDB in 12.0 entfernt hat; nehmen Sie die aktuellen Dateien
([Upgrades und die SQL-Update-Dateien](upgrades-und-sql-update-dateien.md)).

## Eine alte Datenbank nach UTF-8 umwandeln

Eine Datenbank aus einem alten Release in einem Einzelbyte-Zeichensatz wird mit `bin/php/ezconvertdbcharset.php`
(`exp:ezconvertdbcharset`) umgewandelt, "Changes your Exponential database tables to use UTF8". Es unterstützt MySQL,
PostgreSQL und Oracle (`checkDBDriver()` in `kernel/private/classes/commands/ezconvertdbcharset.php`) und lehnt andere
Engines ab. Optionen aus seinem `--help`:

| Option | Bedeutung |
|---|---|
| `--collation=VALUE` | Sortierfolge der umgewandelten Datenbank; der Standard ist `utf8_general_ci`, geben Sie also `utf8mb4_unicode_ci` oder Ihre eigene Wahl ausdrücklich an |
| `--iconv-character-set=VALUE` | Wenn Datenbank und iconv einen Zeichensatz unterschiedlich benennen (zum Beispiel `windows-1252` für iconv und `iso-8859-1` für MySQL) |
| `--extra-xml-attributes`, `--extra-xml-data`, `--extra-serialized-data` | Eigene Datentypen und Tabellen, die XML oder serialisierte Daten enthalten |
| `--skip-class-translations` | Für Datenbanken aus Releases vor den Übersetzungen von Inhaltsklassen |
| `--log-filename=VALUE` | Die iconv-Umwandlungen protokollieren |

Führen Sie es nach einer Sicherung zuerst auf einer Kopie aus und vergleichen Sie eine Stichprobe von Texten mit Akzenten
vorher und nachher. Die Upgrade-Hinweise nennen es als Werkzeug für Tabellen, die noch nicht UTF-8 sind
([11.3](../../installation/11-aktualisieren.md#hinweise-je-datenbank)).

## Checkliste

- [ ] Der Standard der Datenbank ist `utf8mb4`; jede Tabelle ist InnoDB.
- [ ] `var/log/warning.log` zeigt mit eingeschaltetem `AlwaysLog[]=warning` kein "could not set the connection charset".
- [ ] Redakteure wissen, dass Emoji und andere 4-Byte-Zeichen über diese Verbindung auf MySQL und MariaDB nicht
      gespeichert werden können.
- [ ] Alte Update-Dateien mit `SET storage_engine` sind durch aktuelle ersetzt.

## Verwandte Seiten

- [MySQL und MariaDB einrichten](mysql-und-mariadb-einrichten.md)
- [Zeichensätze und Sortierfolgen](zeichensaetze-und-sortierfolgen.md)
- [Upgrades und die SQL-Update-Dateien](upgrades-und-sql-update-dateien.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
