---
title: "Modul 2: Die Template-Sprache"
audience: trainer, developer
category: training
order: 20
summary: Das zweite Modul des Kurses Entwickler. Die Teilnehmenden bauen sich eine Template-Spielwiese und lernen Variablen, Bedingungen, Schleifen, Operatoren, wash, Übersetzungen, die URL-Operatoren, include und die View-Funktionen kennen. 5 Stunden.
keywords: entwickler, modul 2, template-sprache, tpl, def, set, if, foreach, operatoren, wash, i18n, ezurl, ezdesign, include, node_view_gui, attribute_view_gui, developers course, module 2, template language, operators
type: help
---
# Modul 2: Die Template-Sprache

Alles, was ein Besucher sieht, läuft durch Templates. Dieses Modul vermittelt die Template-Sprache von der ersten
Variablen bis zu den Funktionen, die eine Seite zusammensetzen. Die Teilnehmenden probieren jeden Ausdruck in einer
Spielwiese aus, die ein Template auf der Kommandozeile rendert; auf einer Website ändert sich also nichts, bis Modul 3
Templates in ein Design bringt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 5 Stunden (mit zwei Pausen von je 10 Minuten) |
| Lektionen | 6 |
| Niveau | Einsteiger bis Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 2.1: Variablen und Ausgabe](variablen-und-ausgabe.md) | 50 Minuten | Die Spielwiese bauen; `def`, `set`, `undef`, Arrays, Hashes, `wash` |
| [Lektion 2.2: Bedingungen und Logik](bedingungen-und-logik.md) | 45 Minuten | `if`, Vergleiche, `and`/`or`/`not`, `cond`, `first_set`, `switch` |
| [Lektion 2.3: Schleifen mit foreach](schleifen.md) | 45 Minuten | Schlüssel, Trennzeichen, Sequenzen, Offset, Maximum, umgekehrte Reihenfolge, Abbruch |
| [Lektion 2.4: Operatoren, wash und Übersetzungen](operatoren-wash-und-uebersetzungen.md) | 50 Minuten | Operatoren verketten, wash-Typen, Datumsangaben, `i18n`, `attribute( 'show' )` |
| [Lektion 2.5: Links und Design-Dateien](links-und-design-dateien.md) | 40 Minuten | Formen von `ezurl`, `ezroot`, `ezdesign`, `ezimage` |
| [Lektion 2.6: include, node_view_gui und attribute_view_gui](include-und-view-funktionen.md) | 50 Minuten | Include mit Parametern, View-Modi, Felder mit den Templates ihres Datentyps |

## Dieses Modul vorbereiten

- Alle Teilnehmenden haben `extension/mysite_hello` aus Lektion 1.4 und einen funktionierenden Aufruf von
  `ezexec.php`.
- Die Schulungsinstallation hat Inhalte unterhalb von Knoten 2: einige Ordner und Artikel, damit `$node.children` und
  `data_map` etwas zeigen. Ein frisches `exp:install` mit dem Standardpaket genügt.
- Bereiten Sie einen gedruckten oder projizierten Spickzettel der Syntax aus
  [Templates und Design, Teil 6](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten) vor.
- Proben Sie jede Übung in der Spielwiese; die Ausgaben hängen vom Inhalt Ihrer Schulungsinstallation ab, notieren Sie
  also die erwarteten Namen und IDs für Ihre eigene Installation.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 2.1 | 50 |
| Lektion 2.2 | 45 |
| Pause | 10 |
| Lektion 2.3 | 45 |
| Lektion 2.4 | 50 |
| Pause | 10 |
| Lektion 2.5 | 40 |
| Lektion 2.6 | 50 |
| **Gesamt** | **300** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
