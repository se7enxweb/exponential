---
title: "Gesprächspunkte: Hosting, Datenbanken und Freiheit"
audience: advocate
category: advocacy
order: 22
summary: Belegte Gesprächspunkte zur Freiheit, zu wählen, wo Exponential läuft - sechs Datenbanken, Betriebssysteme in vier Stufen, Webserver und Engines, Container und Hosting-Panels, Umzug zwischen Datenbanken - und die ehrlichen Grenzen.
keywords: Gesprächspunkte, Hosting, Datenbanken, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Betriebssysteme, Linux, BSD, macOS, Windows, Container, Shared Hosting, Anbieterbindung, Freiheit, Migration, talking points, hosting, databases, operating systems, containers, shared hosting, lock-in, freedom, migration
type: help
---
# Gesprächspunkte: Hosting, Datenbanken und Freiheit

Diese Seite ist für Fürsprecher, denen die Frage begegnet: „Können wir es auf dem betreiben, was wir haben?“ Die Antwort
ist oft ja, und die Details zählen: Exponential dokumentiert seine Unterstützung in Stufen und Listen, nicht in Slogans.

## Inhalt

1. [Die Punkte](#die-punkte)
2. [Die Datenbanken in je einer Zeile](#die-datenbanken-in-je-einer-zeile)
3. [Die Stufen der Betriebssysteme](#die-stufen-der-betriebssysteme)
4. [Die ehrlichen Grenzen](#die-ehrlichen-grenzen)
5. [Fragen, die Sie hören werden](#fragen-die-sie-hören-werden)

## Die Punkte

| Sagen Sie | Quelle |
|---|---|
| „Sechs Datenbanken werden unterstützt, in dieser offiziellen Reihenfolge: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL.“ | [Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge) |
| „SQLite ist der Standard: eine vollständige Website ohne Datenbankserver.“ | [Datenbankunterstützung, SQLite 3](../datenbankunterstuetzung.md#1-sqlite-3-der-standard), [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md) |
| „Betriebssysteme sind in vier Stufen dokumentiert, von getestet bis theoretisch.“ | [Betriebssystemunterstützung, Die vier Stufen](../betriebssystemunterstuetzung.md#die-vier-stufen) |
| „PHP 8.0 oder später (PHP 7.4 ohne Support nutzbar) auf fast jedem Betriebssystem, mit vielen Webservern.“ | [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md#webserver) |
| „Container, Cloud-Maschinen und Hosting-Panels sind abgedeckt.“ | [Container, Cloud und Hosting-Panels](../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels) |
| „Sie können eine Website mit dokumentierten Werkzeugen in eine andere Datenbank umziehen.“ | [Eine Website in eine andere Datenbank umziehen](../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen) |
| „Backups sind Standard-Dumps für jede Datenbank.“ | [Backup und Wiederherstellung](../datenbankunterstuetzung.md#backup-und-wiederherstellung) |
| „Jeder darf es hosten; Sie sind nicht an einen Anbieter gebunden.“ | [Mit Einwänden umgehen, Hosting und Anbieterbindung](../geschaeft/einwandbehandlung.md#hosting-und-herstellerbindung) |
| „Sie können einen Rechner in zwei Minuten prüfen.“ | [Einen Rechner in zwei Minuten prüfen](../betriebssystemunterstuetzung.md#einen-rechner-in-zwei-minuten-prüfen) |

## Die Datenbanken in je einer Zeile

Lesen Sie die Seite, bevor Sie tiefer gehen; jede Datenbank hat ihren eigenen Abschnitt und ihre Grenzen.

| Datenbank | Sagen Sie |
|---|---|
| SQLite 3 | „Der Standard; kein Server zu betreiben.“ |
| PostgreSQL | „Der empfohlene Datenbankserver.“ |
| MongoDB | „Unterstützt, mit dokumentierten Grenzen.“ ([Grenzen von MongoDB](../datenbankunterstuetzung.md#grenzen-von-mongodb)) |
| MariaDB und MySQL | „Unterstützt; sie teilen sich einen Treiber.“ |
| Oracle | „Unterstützt über die Erweiterung `ezoracle`.“ |
| Microsoft SQL Server | „Mit zusätzlicher Arbeit möglich; nicht eine der sechs.“ ([Abschnitt](../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand)) |

## Die Stufen der Betriebssysteme

| Stufe | Bedeutung |
|---|---|
| Stufe 1 | Hier getestet |
| Stufe 2 | Funktioniert bekanntermaßen |
| Stufe 3 | Sollte funktionieren |
| Stufe 4 | Heute nicht möglich (theoretisch) |

Welches System in welcher Stufe steht: [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md). Nennen Sie immer die Stufe, nicht
nur „es läuft auf X“.

## Die ehrlichen Grenzen

- **Shared Hosting**: Apache oder LiteSpeed mit `.htaccess` können es ausliefern, und der Setup-Assistent installiert es
  ohne Shell; Velocity kann dort meist nicht laufen.
- **MongoDB** hat dokumentierte Grenzen; lesen Sie sie, bevor Sie es empfehlen.
- **Microsoft SQL Server** gehört nicht zu den sechs unterstützten Datenbanken.
- Systeme der **Stufen 3 und 4** sind nicht getestet; sagen Sie das.

## Fragen, die Sie hören werden

| Frage | Antwort |
|---|---|
| „Welche Datenbank sollte eine kleine Website nutzen?“ | SQLite ist der Standard und braucht keinen Server. Für einen Datenbankserver wird PostgreSQL empfohlen. |
| „Können wir mit SQLite anfangen und später umziehen?“ | Ja, die Umzüge zwischen den Datenbanken sind dokumentiert. |
| „Windows?“ | Ja; lesen Sie die Plattformhinweise für Windows unter [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md#windows). |
| „ARM?“ | Lesen Sie [Prozessorarchitekturen](../betriebssystemunterstuetzung.md#prozessorarchitekturen). |
| „Wer hostet es?“ | Jeder. 7x bietet unter anderem Managed Hosting an; Sie wählen ([Dienstleistungen und Hosting](../wer-ist-7x.md#dienstleistungen-und-hosting)). |

## Verwandte Seiten

- [Gesprächspunkte für Administratoren](gespraechspunkte-fuer-administratoren.md)
- [Hosting, Datenbank und Betriebssystem wählen](../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md)
- [Exponential sachlich vergleichen, Keine Bindung](../geschaeft/exponential-sachlich-vergleichen.md#keine-herstellerbindung-punkt-für-punkt)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
