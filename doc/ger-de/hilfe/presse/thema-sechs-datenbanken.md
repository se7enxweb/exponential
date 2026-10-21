---
title: "Thema: sechs Datenbanken"
audience: press
category: press-media
order: 68
summary: Ein Themenbeitrag über die sechs Datenbanken, die Exponential unterstützt - SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle und MySQL, in dieser offiziellen Reihenfolge - wofür jede da ist, wie sie getestet werden und wo die Grenzen liegen, mit druckbaren Fakten und Quellen.
keywords: Thema, Datenbanken, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, Datenbankabstraktion, Presse, story, databases, database abstraction, press
type: help
---
# Thema: sechs Datenbanken

Diese Seite ist für Technikjournalisten, die über Datenbanken schreiben und über Content-Management-Systeme, die auf
mehr als einer laufen. Exponential dokumentiert sechs, in einer offiziellen Rangfolge. Diese Seite nennt diese
Reihenfolge, was die Quellen über jede sagen und wie sie getestet werden.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Die sechs, der Reihe nach](#die-sechs-der-reihe-nach)
3. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
4. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
5. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen](#wen-sie-fragen)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

„Supported databases (Exponential 6.0.x), in order of preference: 1. SQLite 3 (the default) · 2. PostgreSQL ·
3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work at
this time“ ([README, Requirements](https://github.com/se7enxweb/exponential/blob/main/README.md#requirements)) – die
unterstützten Datenbanken in der bevorzugten Reihenfolge, wobei Microsoft SQL Server derzeit mit zusätzlichem Aufwand
möglich ist. Die Reihenfolge ist schon eine Aussage für sich: eine Dateidatenbank ohne Server zuerst, eine
Dokumentendatenbank an dritter Stelle und MySQL, lange die übliche Wahl für PHP-Anwendungen, zuletzt. Ein Befehl
installiert eine Website auf fünf davon ([Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md#choose-a-database)).

## Die sechs, der Reihe nach

| # | Datenbank | Was die Quellen sagen | Quelle |
|---|---|---|---|
| 1 | SQLite 3 | Die Voreinstellung; eine Datei, kein Server | [Datenbankunterstützung](../datenbankunterstuetzung.md#1-sqlite-3-der-standard) |
| 2 | PostgreSQL | Der empfohlene Datenbankserver | [Gesprächspunkte: Hosting](../fuersprecher/gespraechspunkte-hosting-datenbanken-freiheit.md#die-datenbanken-in-je-einer-zeile) |
| 3 | MongoDB | Unterstützt seit 6.0.14, mit dokumentierten Grenzen | [MongoDB als Datenbank](../../../eng-us/features/6.0/mongodb-database-support.md) |
| 4 | MariaDB | Unterstützt; teilt einen Treiber mit MySQL | [Datenbankunterstützung, Auf einen Blick](../datenbankunterstuetzung.md#auf-einen-blick) |
| 5 | Oracle | Unterstützt über die Erweiterung `ezoracle` | ebenda |
| 6 | MySQL | Unterstützt; teilt einen Treiber mit MariaDB | ebenda |

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| SQLite kommt als Datenbank hinzu, die keinen Server braucht | Januar 2024 | [Januar 2024](../../../eng-us/history/2024/2024-01a.md) |
| MongoDB 8 als Datenbank, in 6.0.14 | Juni 2026 (6.0.14 am 7. Juni) | [Juni 2026](../../../eng-us/history/2026/2026-06a.md) |
| Korrekturen an Treibern und Installationsprogrammen für alle Engines; SQLite ist die Voreinstellung des Setup-Assistenten | 16. bis 30. September 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#16-to-30-september-2026) |
| Oracle 11g und 12.1 (ezoracle 2.3.7) | 14. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#14-october-2026-oracle-11g-and-121-ezoracle-237) |

Die Änderungen von September und Oktober gehören zur Produktlinie 6.0.15, die in Entwicklung und nicht erschienen ist.

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Die offizielle Reihenfolge ist SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL. | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#requirements) |
| Die CI des Projekts führt Datenbanktests auf PostgreSQL 15 und MySQL 8.0 aus; SQLite und MongoDB haben Unit-Tests für Treiber oder Adapter; MariaDB und Oracle sind nicht in der CI. | [Datenbankunterstützung, Auf einen Blick](../datenbankunterstuetzung.md#auf-einen-blick) |
| SQLite kann nicht von mehreren Webservern gemeinsam genutzt werden; die fünf Server können das. | ebenda |
| Dokumentierte Werkzeuge ziehen eine Website von einer Datenbank in eine andere um. | [Eine Website umziehen](../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen) |
| Microsoft SQL Server ist „possible with additional work“, mit zusätzlichem Aufwand möglich, und keine der sechs. | [Datenbankunterstützung](../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand) |

## Was Sie nicht behaupten sollten

- „Jede Funktion läuft auf jeder Datenbank.“ MongoDB hat eine dokumentierte Liste von Grenzen, und `eztags` ist dort
  nicht nutzbar ([MongoDB, Was funktioniert und was nicht](../../../eng-us/features/6.0/mongodb-database-support.md#what-works-and-what-does-not-june-2026)).
- „In der CI auf allen sechs getestet.“ Nur PostgreSQL und MySQL durchlaufen in der CI vollständige Datenbanktests.
- „Unterstützt SQL Server.“ Heute nicht.
- Jede Leistungsrangliste der Datenbanken: Die Quellen veröffentlichen keine.

## Blickwinkel und Ideen für Überschriften

```text
"MySQL last: Exponential's surprising database order"
"Six databases, one content model"
"From one file to Oracle: choosing a database for an open source CMS"
```

Auf Deutsch zum Beispiel: „MySQL zuletzt: die überraschende Datenbank-Reihenfolge von Exponential“, „Sechs Datenbanken,
ein Inhaltsmodell“, „Von einer Datei bis Oracle: eine Datenbank für ein Open-Source-CMS wählen“.

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Warum diese Reihenfolge, Pläne für weitere | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Datenbankentscheidungen in der Praxis | die Community | <https://share.exponential.earth/forums> |

## Verwandte Seiten

- [Datenbankunterstützung](../datenbankunterstuetzung.md)
- [Thema: SQLite als Voreinstellung](thema-sqlite-als-standard.md)
- [Thema: MongoDB als Datenbank](thema-mongodb-als-datenbank.md)
- [Funktionsthemen: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
