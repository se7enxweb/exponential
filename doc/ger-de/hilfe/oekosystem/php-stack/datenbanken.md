---
title: Datenbanken im Ökosystem
audience: ecosystem, admin, hosting, developer
category: ecosystem
order: 32
summary: Die Datenbanken des Ökosystems von Exponential in der offiziellen Reihenfolge des Projekts - SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle und MySQL -, wofür jede gewählt wird, was die CI testet, der Stand bei Microsoft SQL Server und wo sich die Platform-Linie unterscheidet.
keywords: Datenbanken, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, Reihenfolge der Bevorzugung, CI, Treiber, databases, order of preference, drivers
type: help
---
# Datenbanken im Ökosystem

> "**Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time." ([Datenbankunterstützung](../../datenbankunterstuetzung.md))
>
> Also: unterstützte Datenbanken in der Reihenfolge der Bevorzugung sind SQLite 3 (Standard), PostgreSQL, MongoDB,
> MariaDB, Oracle und MySQL; Microsoft SQL Server ist derzeit mit zusätzlicher Arbeit bekanntermaßen möglich.

## Inhalt

1. [Die sechs Datenbanken](#die-sechs-datenbanken)
2. [Microsoft SQL Server](#microsoft-sql-server)
3. [Die Platform-Linie](#die-platform-linie)
4. [Wo Sie mehr lesen](#wo-sie-mehr-lesen)

## Die sechs Datenbanken

| # | Datenbank | Gewählt für | Server nötig | In der CI getestet |
|---|---|---|---|---|
| 1 | SQLite 3 | Der Standard; "a full production database for a site served from one machine", eine vollwertige Produktionsdatenbank für eine Website auf einem Rechner | Nein: eine Datei | Unit-Tests des Treibers |
| 2 | PostgreSQL | "the recommended database server", der empfohlene Datenbankserver | Ja | Ja (PostgreSQL 15) |
| 3 | MongoDB | "organizations standardized on MongoDB", Organisationen, die auf MongoDB festgelegt sind, über einen Adapter | Ja | Unit-Tests des Adapters |
| 4 | MariaDB | "the MySQL-compatible server the project prefers", der MySQL-kompatible Server, den das Projekt bevorzugt | Ja | Nein |
| 5 | Oracle | 19c und neuer, über `ezoracle`, in jeder Installation vorhanden | Ja | Nein |
| 6 | MySQL | Unterstützt über denselben Treiber wie MariaDB | Ja | Ja (MySQL 8.0) |

Quelle: [Datenbankunterstützung](../../datenbankunterstuetzung.md).

## Microsoft SQL Server

"Microsoft SQL Server has no driver in Exponential 6.0.x. It is known to be possible", es gibt also keinen Treiber,
aber es ist bekanntermaßen möglich, und "An extension written for the 4.x line, `se7enxweb/xrowmssql`, is the natural
starting point for that work", eine für die Linie 4.x geschriebene Erweiterung ist der natürliche Ausgangspunkt
([Datenbankunterstützung](../../datenbankunterstuetzung.md)). Eine Gelegenheit für Beiträge aus dem Ökosystem.

## Die Platform-Linie

Die Symfony-Distributionen verwenden ihre eigene Datenbankschicht. Der Beitrag des Ökosystems dort ist die
Unterstützung für SQLite: Die DXP "features improvements not found in Ibexa DXP OSS", hat also Verbesserungen, die es
in Ibexa DXP OSS nicht gibt, beginnend mit "The SQLite database driver and framework support" (`README.md`); siehe
[SQLite für die Plattform](../../../funktionen/6.0/plattform-sqlite-installation.md).

## Wo Sie mehr lesen

- Für Hoster eine Seite pro Datenbank: [Hosting-Grundlagen](../../hosting/grundlagen/README.md).
- Die Engine einer laufenden Website wechseln: [Die Datenbank-Engine wechseln](../../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md).
- Die Funktion SQLite: [SQLite-Datenbank](../../../funktionen/6.0/sqlite-datenbank.md); MongoDB:
  [Unterstützung für MongoDB-Datenbanken](../../../funktionen/6.0/mongodb-datenbankunterstuetzung.md).

## Verwandte Seiten

- [Der PHP-Stack](README.md)
- [Datenbankunterstützung](../../datenbankunterstuetzung.md)
- [Datenbanken in der offiziellen Reihenfolge](../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
- [Argumente: Hosting, Datenbanken und Freiheit](../../fuersprecher/gespraechspunkte-hosting-datenbanken-freiheit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
