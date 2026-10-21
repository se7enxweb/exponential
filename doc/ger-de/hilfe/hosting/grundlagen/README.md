---
title: Grundlagen des Hostings
audience: hosting
category: hosting
order: 10
summary: Der Überblick über den Bereich Grundlagen des Hostings - was ein Server braucht, um Exponential zu betreiben, die PHP-Versionen, Erweiterungen und Einstellungen, die sechs unterstützten Datenbanken in der offiziellen Reihenfolge, Betriebssysteme, Composer und Git, Berechtigungen, Dimensionierung, Bildverarbeitung und Suche.
keywords: Hosting, Grundlagen, Überblick, Anforderungen, PHP, Erweiterungen, php.ini, Datenbanken, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, SQL Server, Betriebssysteme, Composer, Git, Berechtigungen, Dimensionierung, Bilder, Suche, Solr, hosting, basics, overview, requirements, extensions, databases, operating systems, permissions, sizing, images, search
type: help
---
# Grundlagen des Hostings

Dieser Bereich ist für alle bei einem Hosting-Anbieter, die wissen müssen, was Exponential von einem Server verlangt,
bevor ein Angebot entworfen, ein Server gebaut oder einem Kunden geholfen wird: Produktmanager, Systemadministratoren und
Support-Mitarbeitende. Er beantwortet eine Frage pro Seite: Was braucht die Software, und was bedeutet das für die Art,
wie Sie sie hosten? Jede Tatsache auf diesen Seiten stammt aus dem Repository (dem Installationsbuch in
`doc/eng-us/install/`, den Hilfeseiten zur [Datenbankunterstützung](../../datenbankunterstuetzung.md) und
[Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md) sowie den mitgelieferten Einstellungen in `settings/`), und jede
Seite verlinkt ihre Quellen, damit Sie sie prüfen können.

Zurück zum Anfang der Hilfe zum Hosting: [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md).

## Inhalt

1. [Wie Sie diesen Bereich lesen](#wie-sie-diesen-bereich-lesen)
2. [Die Seiten](#die-seiten)
3. [Die Kurzfassung](#die-kurzfassung)
4. [Verwandte Seiten](#verwandte-seiten)

## Wie Sie diesen Bereich lesen

| Wenn Sie ... sind | Beginnen Sie mit |
|---|---|
| Produktmanager und entwerfen ein Angebot | [Was das Hosting von Exponential umfasst](was-das-hosting-von-exponential-umfasst.md), dann [Anforderungen auf einen Blick](anforderungen-auf-einen-blick.md) und [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md) |
| Systemadministrator und bauen einen Server | [Anforderungen auf einen Blick](anforderungen-auf-einen-blick.md), [PHP-Versionen und Erweiterungen](php-versionen-und-erweiterungen.md), [PHP-Einstellungen für Exponential](php-einstellungen-fuer-exponential.md), [Beschreibbare Verzeichnisse und Berechtigungen](beschreibbare-verzeichnisse-und-berechtigungen.md) |
| im Support und antworten einem Kunden | [Anforderungen auf einen Blick](anforderungen-auf-einen-blick.md) und die Seite der Datenbank des Kunden |
| dabei, Kapazität zu planen | [Speicher, CPU und Festplatte dimensionieren](speicher-cpu-und-festplatte-dimensionieren.md) |

## Die Seiten

| Seite | Was sie beantwortet |
|---|---|
| [Was das Hosting von Exponential umfasst](was-das-hosting-von-exponential-umfasst.md) | Die Teile, die ein Hoster bereitstellt (PHP, Datenbank, Webserver oder Velocity, Cron, E-Mail, Speicher, Backups), und wer meist was übernimmt |
| [Anforderungen auf einen Blick](anforderungen-auf-einen-blick.md) | Eine Tabelle mit allem, was ein Server braucht, mit Minimum, Empfehlung und dem, was es prüft |
| [PHP-Versionen und Erweiterungen](php-versionen-und-erweiterungen.md) | PHP 8.0 bis 8.5, die benötigten und empfohlenen Erweiterungen und die Paketnamen je Systemfamilie |
| [PHP-Einstellungen für Exponential](php-einstellungen-fuer-exponential.md) | `php.ini`-Werte, die der Installer prüft, die Werte für einen Pool, OPcache und die Funktionen, die Sie nicht deaktivieren dürfen |
| [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md) | Die sechs unterstützten Datenbanken, in Reihenfolge, nebeneinander, und wie Sie wählen, was Sie anbieten |
| [SQLite für Hoster](sqlite-fuer-hoster.md) | Die Standarddatenbank: eine Datei, ihre Berechtigungen, Backups und Grenzen |
| [PostgreSQL für Hoster](postgresql-fuer-hoster.md) | Der empfohlene Datenbankserver: Eigentümer, `pgcrypto`, Backups |
| [MongoDB für Hoster](mongodb-fuer-hoster.md) | Der MongoDB-Adapter: Anforderungen, der Benutzer, Indizes und Grenzen |
| [MariaDB für Hoster](mariadb-fuer-hoster.md) | MariaDB über den Treiber `mysqli`: Zeichensatz, Rechte, Backups |
| [Oracle für Hoster](oracle-fuer-hoster.md) | Oracle 19c und später über `ezoracle`: `oci8`, der Benutzer, DRCP |
| [MySQL für Hoster](mysql-fuer-hoster.md) | MySQL über den Treiber `mysqli`: Rechte, InnoDB, Replikate |
| [Stand von Microsoft SQL Server](stand-von-microsoft-sql-server.md) | Warum es in 6.0.x keinen Treiber gibt und was nötig wäre |
| [Betriebssysteme für Hoster](betriebssysteme-fuer-hoster.md) | Die vier Stufen, die Referenzplattform, Control Panels und Container |
| [Composer und Git auf Servern](composer-und-git-auf-servern.md) | Wie der Code ankommt, wie Erweiterungen Pakete sind, was Sie auf einer laufenden Website nicht ausführen sollten |
| [Beschreibbare Verzeichnisse und Berechtigungen](beschreibbare-verzeichnisse-und-berechtigungen.md) | Was beschreibbar sein muss, ein Benutzer oder eine Gruppe, die gehärteten Dateimodi |
| [Speicher, CPU und Festplatte dimensionieren](speicher-cpu-und-festplatte-dimensionieren.md) | Die Zahlen, die das Repository nennt, und wie Sie den Rest messen |
| [Bildverarbeitung und Medien](bildverarbeitung-und-medien.md) | GD und ImageMagick, Bild-Aliase, akzeptierte Upload-Typen |
| [Such-Backends](such-backends.md) | Die eingebaute Suche, verzögerte Indizierung und Solr über eZ Find |

## Die Kurzfassung

- **PHP 8.0 oder später** für den Kernel und für Exponential Velocity, den empfohlenen Webserver, mit `sockets`
  (ab Velocity 0.0.4.54; [Kapitel 2](../../../installation/02-voraussetzungen.md#22-php)).
- **Datenbanken, in der offiziellen Reihenfolge:** 1. SQLite 3 (Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB ·
  5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich (kein Treiber
  in 6.0.x). Siehe [Datenbankunterstützung](../../datenbankunterstuetzung.md).
- **Beschreibbar:** immer `var/` und `settings/`; `design/` und `extension/`, solange Pakete aus der Administration
  installiert werden ([Kapitel 8](../../../installation/08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer)).
- **Zugang zur Kommandozeile** ist wichtig: Composer, Cronjobs, das Leeren von Caches und die Konsole laufen alle mit dem
  PHP der Kommandozeile.
- **Der Installer prüft die Maschine für Sie:** Die Systemprüfung des Setup-Assistenten, der Kickstarter und
  `exp:install` führen dieselben kritischen Tests aus und geben für jeden Fehler die Lösung aus.

## Verwandte Seiten

- [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md)
- [Hosting-Modelle](../modelle/README.md) und [Control Panels](../control-panels/README.md)
- [Webserver und Engines](../server/README.md) und [Leistung](../leistung/README.md)
- [Betrieb](../betrieb/README.md), [Sicherheit](../sicherheit/README.md) und [Bereitstellung](../bereitstellung/README.md)
- [Kapitel 2: Anforderungen](../../../installation/02-voraussetzungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
