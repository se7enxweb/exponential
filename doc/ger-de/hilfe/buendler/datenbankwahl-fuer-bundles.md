---
title: Datenbankwahl für Bundles
audience: bundler, hosting
category: bundling
order: 240
summary: Welche Datenbanken ein Bundle von Exponential 6.0.x verwenden kann, in der offiziellen Reihenfolge des Projekts (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), was jede im Bundle braucht, wie jeder Installer sie benennt und welche Datenbank zu welcher Art von Bundle passt.
keywords: Datenbank, Datenbankwahl, database, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, DatabaseImplementation, --db, Type, pdo, sqlite3, pgsql, mysqli, oci8, bundle
type: help
---
# Datenbankwahl für Bundles

Diese Seite richtet sich an Bündler, die entscheiden, welche Datenbank ihr Bundle verwendet oder anbietet. Die
Unterstützungsaussage und alle Einzelheiten stehen unter [Datenbankunterstützung](../datenbankunterstuetzung.md); diese
Seite wendet sie auf Bundles an.

## Inhalt

1. [Die offizielle Reihenfolge](#die-offizielle-reihenfolge)
2. [Was jede im Bundle braucht](#was-jede-im-bundle-braucht)
3. [Den Installern die Datenbank nennen](#den-installern-die-datenbank-nennen)
4. [Welche Datenbank für welches Bundle](#welche-datenbank-für-welches-bundle)
5. [Was ein Bundle nicht versprechen darf](#was-ein-bundle-nicht-versprechen-darf)

## Die offizielle Reihenfolge

> **Unterstützte Datenbanken (Exponential 6.0.x) in der Reihenfolge der Empfehlung:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit nachweislich mit
> zusätzlichem Aufwand möglich. Einzelheiten: [Datenbankunterstützung](../datenbankunterstuetzung.md).

"Alle sechs werden unterstützt: Jede hat einen Treiber im Kernel oder in einer mitgelieferten Erweiterung, einen
Eintrag im Einrichtungsassistenten und einen Weg in den Kommandozeilen-Installern"
([Die unterstützten Datenbanken, in Reihenfolge](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)).

## Was jede im Bundle braucht

| Datenbank | `DatabaseImplementation` | PHP-Erweiterung | Server im Bundle |
|---|---|---|---|
| 1. SQLite 3 | `sqlite3` | `sqlite3` | keiner: eine Datei unterhalb von `var/storage/sqlite3/` |
| 2. PostgreSQL | `ezpostgresql` | `pgsql` | ja (in der Praxis 13 oder neuer) |
| 3. MongoDB | `mongodb` | `mongodb` plus die Bibliothek `mongodb/mongodb`; PHP 8.2 oder neuer | ja (6.0 oder neuer) |
| 4. MariaDB | `ezmysqli` | `mysqli` | ja |
| 5. Oracle | `ezoracle` | `oci8` und die Erweiterung `ezoracle` (von der `composer.json` verlangt) | ja (19c oder neuer) |
| 6. MySQL | `ezmysqli` | `mysqli` | ja |

Aus [Auf einen Blick](../datenbankunterstuetzung.md#auf-einen-blick) und
[Versionen, am Code geprüft](../datenbankunterstuetzung.md#versionen-am-code-überprüft). Verwenden Sie
Serverversionen, "die Ihr Hersteller noch mit Sicherheitskorrekturen versorgt".

## Den Installern die Datenbank nennen

| Datenbank | `exp:install --db=` | `kickstart.ini [database_choice] Type=` |
|---|---|---|
| SQLite 3 | `sqlite` (Standard) | `sqlite3` (Standard) |
| PostgreSQL | `pgsql` | `pgsql` |
| MongoDB | `mongodb` | `mongodb` |
| MariaDB | `mariadb` | `mysqli` |
| Oracle | `oracle` | `oci8` |
| MySQL | `mysql` | `mysqli` |

"Keiner der Installer legt die Datenbank oder den Benutzer eines Datenbankservers an. Legen Sie sie zuerst an"
([Installer-Unterstützung je Weg](../datenbankunterstuetzung.md#unterstützung-durch-die-installer)). Das übernimmt der erste Start
Ihres Bundles oder Ihr Panel.

> **Hinweis**
> Der Konfigurationsschritt für MongoDB "kann im Einrichtungsassistenten ein bis drei Minuten dauern"; geben Sie dem
> Installationsschritt eines Bundles genug Zeit oder verwenden Sie die Konsole.

## Welche Datenbank für welches Bundle

| Bundle | Datenbank | Warum |
|---|---|---|
| Demo, Schulungsraum, Laptop, Appliance mit einer Maschine | SQLite 3 | kein Server, der separat installiert, abgesichert oder gesichert werden muss ([SQLite-Bundles](sqlite-bundles.md)) |
| Image für einen einzelnen Server mit einer echten Website | SQLite 3 oder PostgreSQL | SQLite für eine Maschine; PostgreSQL, wenn der Empfänger einen Server möchte |
| Container-Plattform mit mehreren Replikaten | PostgreSQL (oder ein anderer Server) | "Mehrere Container, die eine Website ausliefern, brauchen einen Datenbankserver ('PostgreSQL zuerst')" ([Container](../hosting/modelle/container-docker-und-podman.md#die-datenbank-und-die-regel-eines-einzigen-schreibers)) |
| Panel-Installer | was das Panel bereitstellt; oft MariaDB oder MySQL, PostgreSQL, wo angeboten | das Panel legt Datenbank und Benutzer an ([Datenbanken und Benutzer in Panels](../hosting/control-panels/datenbanken-und-benutzer.md)) |
| Organisation mit einer Standarddatenbank | diese Datenbank | alle sechs werden unterstützt |

## Was ein Bundle nicht versprechen darf

- **Microsoft SQL Server.** "Microsoft SQL Server hat in Exponential 6.0.x keinen Treiber"; er ist mit zusätzlichem
  Aufwand "nachweislich möglich"
  ([Datenbankunterstützung](../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand)).
- **Mehrere Webserver auf einer SQLite-Datei.** "Mehrere Webserver auf einer Datenbank: nein" für SQLite.
- **Ungetestete Kombinationen.** Die CI testet PostgreSQL 15 und MySQL 8.0 sowie die Unit-Tests der Treiber; testen Sie
  die Datenbank, die Ihr Bundle ausliefert ([Ein Bundle testen](ein-bundle-testen.md)).

## Verwandte Seiten

- [Datenbankunterstützung](../datenbankunterstuetzung.md)
- [Kapitel 9: Datenbanken](../../installation/09-datenbanken.md)
- [Datenbanken in der offiziellen Reihenfolge, für Hoster](../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
