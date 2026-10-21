---
title: "Thema: SQLite als Standard"
audience: press
category: press-media
order: 69
summary: Ein Themenbeitrag über SQLite als Standarddatenbank von Exponential - eine vollständige Website in einer Datei ohne Datenbankserver, seit Januar 2024 - mit dem, was der Treiber ergänzt, wann es passt und wann nicht, druckbaren Fakten und Quellen.
keywords: Thema, SQLite, SQLite 3, Standarddatenbank, kein Datenbankserver, eine Datei, Schreibwarteschlange, WAL, kleine Websites, Presse, story, default database, no database server, one file, write queue, small sites, press
type: help
---
# Thema: SQLite als Standard

Diese Seite ist für Journalisten und Creator, die über einfaches Hosting, kleine Organisationen, Bildung oder
Entwickler-Laptops schreiben. Die Standarddatenbank von Exponential ist SQLite: die ganze Website in einer Datei. Diese
Seite erklärt das Thema, wann es passt und was Sie nicht versprechen sollten.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Wann es passt, und wann nicht](#wann-es-passt-und-wann-nicht)
5. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen können](#wen-sie-fragen-können)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

„With SQLite, a complete Exponential site runs from one file on disk, with no database server to install, configure
or secure“ ([Unterstützung der SQLite-Datenbank](../../../eng-us/features/6.0/sqlite-database.md)). Es kam in den ersten Monaten
der Linie 6.0 im Januar 2024 und wurde zum Standard: des Einrichtungsassistenten im September 2026 und des
Installationsprogramms mit einem Befehl (`./console exp:install`), das auf SQLite installiert, sofern nichts anderes
angegeben ist ([Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md)). Das README nennt es „an
incredible improvement to the kernel“ ([README, Recent improvements](https://github.com/se7enxweb/exponential/blob/main/README.md#recent-improvements-to-exponential)).

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| 6.0.0 ist stabil, und SQLite kommt als Datenbank, die keinen Server braucht | Januar 2024 | [Januar 2024](../../../eng-us/history/2024/2024-01a.md) |
| SQLite-Unterstützung in den Releases 6.0.1 bis 6.0.3, seitdem verfeinert | Januar bis April 2024 | [Unterstützung der SQLite-Datenbank](../../../eng-us/features/6.0/sqlite-database.md) |
| SQLite ist der Standard des Einrichtungsassistenten; Installation mit einem Befehl auf SQLite | 16. bis 30. September 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#16-to-30-september-2026) |
| Eine Schreibwarteschlange: Gleichzeitige Veröffentlichungen warten, bis sie an der Reihe sind, statt mit „database is locked“ zu scheitern | 1. und 2. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#1-and-2-october-2026) |

Die Änderungen von 2026 sind in der Entwicklungslinie 6.0.15, die nicht veröffentlicht ist.

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| SQLite 3 steht in der offiziellen Reihenfolge der Datenbanken an erster Stelle und ist der Standard. | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#requirements) |
| Die Datenbank besteht aus vier Dateien: der Datenbank, `-wal`, `-shm` und `.writer-lock`. | [Unterstützte Datenbanken, SQLite 3](../datenbankunterstuetzung.md#1-sqlite-3-der-standard) |
| Write-Ahead-Logging lässt Seitenaufrufe weiterlesen, während ein Redakteur veröffentlicht. | ebenda |
| Eine wartende Transaktion wartet standardmäßig bis zu 25 Sekunden (`SQLiteTransactionWait`). | ebenda |
| „The project's own load tests had concurrent readers scale with the number of processes and 16 concurrent writers complete without errors, queued.“ | ebenda |
| Auch Exponential Platform Legacy bietet SQLite an („now with SQLite support“). | [Exponential Platform Legacy](../wer-ist-die-exponential-foundation.md#exponential-platform-legacy) |

## Wann es passt, und wann nicht

| Passt | Wählen Sie stattdessen einen Datenbankserver |
|---|---|
| Ein Rechner, beliebig viele Leser, ein Redaktionsteam | Mehr als ein Rechner muss in die Datenbank schreiben |
| Exponential ausprobieren, ein Laptop, automatisierte Tests, eine kleine Website | Viele Redakteure veröffentlichen den ganzen Tag gleichzeitig große Änderungen |
| Ein Hosting-Tarif ohne MySQL | Sie brauchen Replikation oder Benutzer und Berechtigungen auf Datenbankebene |

Quelle: [Unterstützte Datenbanken, SQLite 3](../datenbankunterstuetzung.md#1-sqlite-3-der-standard) und
[SQLite, Grenzen und gute Praxis](../../../eng-us/features/6.0/sqlite-database.md#limits-and-good-practice).

## Was Sie nicht behaupten sollten

- „SQLite reicht für jede Website.“ Die Quellen nennen, wann ein Server die bessere Wahl ist.
- „Überhaupt keine Datenbank.“ Es gibt eine Datenbank; sie ist eine Datei, kein Server.
- „Schneller als MySQL.“ Die Quellen veröffentlichen keinen Vergleich.

## Blickwinkel und Ideen für Überschriften

```text
"A whole website in one file: Exponential makes SQLite the default"
"No database server to secure: the case for SQLite in a CMS"
"Sixteen writers, no errors: how Exponential queues SQLite publishes"
```

## Wen Sie fragen können

| Frage | Fragen Sie | Wo |
|---|---|---|
| Die Arbeit an SQLite | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Erfahrungen mit kleinen Websites | die Community | <https://share.exponential.earth/forums> |

## Verwandte Seiten

- [Thema: sechs Datenbanken](thema-sechs-datenbanken.md)
- [Thema: Installation mit einem Befehl](thema-installation-mit-einem-befehl.md)
- [Unterstützung der SQLite-Datenbank](../../../eng-us/features/6.0/sqlite-database.md)
- [Themenbeiträge: Übersicht](../../../eng-us/help/press/feature-stories-index.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
