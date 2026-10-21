---
title: "Modul 5: Vom 5.x-Legacy-Stack"
audience: trainer, admin, developer
category: training
order: 50
summary: Das Modul zum Umzug einer Website aus der 5.x-Ära (dem Stack mit zwei Kerneln aus Symfony und dem Legacy-Kernel) zu Exponential 6 - die Variante erkennen, den Legacy-Teil herauslösen, die eingespeiste Konfiguration als INI schreiben und Feldtypen, Bildvarianten und Twig-Templates behandeln. Drei Lektionen, 2 Stunden 45 Minuten.
keywords: migration zu exponential, zwei kernel, herauslösen, bildvarianten, twig zu tpl, modul, migrating to exponential, 5.x, dual kernel, ezpublish_legacy, extraction, ezpublish.yml, INI, siteaccess, ezrichtext, image variations, Twig to TPL, module
type: help
---
# Modul 5: Vom 5.x-Legacy-Stack

Websites der 5.x-Ära betrieben neben dem Legacy-Kernel einen Symfony-Stack, auf einer Datenbank. Exponential führt den
Legacy-Kernel fort, deshalb löst diese Migration den Legacy-Teil aus der Symfony-Anwendung heraus und schreibt auf,
was früher die Symfony-Seite bereitgestellt hat. Das Modul folgt Kapitel 15 des Installationshandbuchs; die
Datenbankschritte sind die aus Modul 4.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Dauer | 2 Stunden 45 Minuten |
| Lektionen | 3 |
| Niveau | Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [5.1 Welche 5.x-Variante und was übernommen wird](welche-variante-und-was-uebernommen-wird.md) | 45 Minuten | Eine Variante an ihren Dateien erkennen; die eigenen Tabellen von Exponential auflisten; über das Schicksal von drei Symfony-Bundles entscheiden |
| [5.2 Den Legacy-Teil herauslösen und die Konfiguration als INI schreiben](legacy-herausloesen-und-ini-schreiben.md) | 60 Minuten | Mit `exp:ini` lesen, woher Einstellungen kommen; YAML-Siteaccess-Zuordnung und Parameter in INI übersetzen |
| [5.3 Feldtypen, Bildvarianten und Twig-Templates](feldtypen-bilder-und-templates.md) | 60 Minuten | Datentypen vergleichen; eine Bildvariante und einen Override-Block schreiben; Twig als TPL neu schreiben; ein DocBook-Fragment umwandeln |

## Dieses Modul vorbereiten

- [ ] Modul 4 zuerst unterrichtet, oder mindestens Lektion 4.2: Die Datenbankkette ist ab 5.4 dieselbe.
- [ ] Eine Schulungsinstallation pro Person mit Shell-Zugang; `sqlite3` installiert.
- [ ] Die YAML-Ausschnitte aus den Lektionen 5.2 und 5.3 ausgedruckt oder in einem gemeinsamen Dokument.
- [ ] Wenn Sie eines haben, die Auflistung des Wurzelverzeichnisses eines alten 5.x-Projekts (ohne Zugangsdaten), um
      sie in Lektion 5.1 am Beamer zu zeigen.
- [ ] Lektion 5.3 ist für Entwickler und Theme-Designer; Administratoren können stattdessen den INI-Teil aus Lektion
      5.2 für einen zweiten Siteaccess wiederholen.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 5.1 | 45 |
| Lektion 5.2 | 60 |
| Pause | 15 |
| Lektion 5.3 | 60 |
| Gesamt | 180 (2 Stunden 45 Minuten Lektionen, zuzüglich einer Pause von 15 Minuten) |

## Verwandte Seiten

- [Migration zu Exponential: Kursüberblick](../README.md)
- [Kapitel 15: Migration vom 5.x-Legacy-Stack](../../../../installation/15-migration-von-5x-legacy.md): das Kapitel, auf
  dem dieses Modul aufbaut.
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
