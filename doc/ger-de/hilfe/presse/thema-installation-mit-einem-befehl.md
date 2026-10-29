---
title: "Thema: Installation mit einem Befehl"
audience: press
category: press-media
order: 80
summary: Ein Themenbeitrag über die Installation von Exponential mit einem Konsolenbefehl auf SQLite, ohne Datenbankserver und ohne Konfigurationsdatei - was der Befehl tut, die Auswahl an Datenbanken, der Kickstarter und der Paketmanager in der Konsole - mit Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, Installation, installieren, ein Befehl, exp:install, SQLite, Kickstarter, ezpm, Einrichtungsassistent, erste Schritte, Schnellstart, story, install, one command, setup wizard, getting started, quick start
type: help
---
# Thema: Installation mit einem Befehl

Diese Seite ist für Journalisten und Creator, die zeigen möchten, wie schnell sich Exponential ausprobieren lässt. Seit
Juli 2026 installiert sich eine vollständige Website mit einem Befehl.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

„Diese Seite ist für alle, die in wenigen Minuten eine funktionierende Exponential-Website möchten, ohne eine
`kickstart.ini` zu schreiben und ohne Datenbankserver“
([Exponential mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md)). `./console exp:install --apply`
installiert das Multisite-Paket auf SQLite in amerikanischem Englisch, mit einer Website und einer Administration, und
gibt die Anmeldeadressen und eine Zusammenfassung aus. Optionen wählen stattdessen MySQL, PostgreSQL, MongoDB oder
Oracle.

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| `ezpm` in der Konsole, ein Installer mit einem Befehl und der Kickstarter | Juli 2026 | [Juli 2026](../../geschichte/2026/2026-07.md) |
| SQLite als Datenbank, die keinen Server braucht | 7. Januar 2024 | [Januar 2024](../../geschichte/2024/2024-01a.md) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Ein Befehl: `./console exp:install --apply`, gleichbedeutend mit `php bin/php/install.php --apply` | [Exponential mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md) |
| Standard: SQLite, `eng-US`, die Siteaccesses `site` und `admin` | ebenda |
| Datenbankoptionen: `sqlite` (Standard), `mysql`/`mariadb`, `pgsql`, `mongodb`, `oracle` | [Eine Datenbank wählen](../../funktionen/6.0/installation-mit-einem-befehl.md#eine-datenbank-wählen) |
| Der Kickstarter installiert für automatisierte Einrichtungen aus einer Konfigurationsdatei | [Kickstarter-CLI](../../funktionen/6.0/kickstarter-cli.md) |
| `ezpm`, ein Paketmanager in der Konsole | [ezpm](../../funktionen/6.0/ezpm-paketmanager-cli.md) |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Installiert sich auf jedem Shared Hosting mit einem Klick“ | Ein Konsolenbefehl auf einer Maschine mit PHP und dem Code |
| „Keine Voraussetzungen“ | PHP 8 und die dokumentierten Erweiterungen sind nötig ([Den Code beschaffen](../../installation/03-den-code-beschaffen.md)) |
| „Mit einem Befehl produktionsreif“ | Eine funktionierende Website zum Ausprobieren; für den Produktivbetrieb brauchen Sie den Härtungsleitfaden ([Sicherheitshärtung](../sicherheitshaertung.md)) |

## Blickwinkel und Ideen für Überschriften

- „Von null zum CMS mit einem Befehl, ohne Datenbankserver.“
- Video: [Demo-Skript: Installation von null](demo-skript-installation-von-null.md).

## Verwandte Seiten

- [Thema: SQLite als Standard](thema-sqlite-als-standard.md)
- [Eine Demo-Installation einrichten](eine-demo-installation-einrichten.md)
- [Erste Schritte](../erste-schritte.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
