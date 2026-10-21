---
title: "Modul 8: Einstellungen und Siteaccesses"
audience: trainer, developer
category: training
order: 80
summary: Das achte Modul des Kurses Entwickler. Die Teilnehmenden verfolgen, wie INI-Dateien gelesen und zusammengeführt werden, legen Einstellungen für einen Siteaccess im richtigen Geltungsbereich ab, ändern und verschieben Einstellungen sicher mit exp:ini und geben ihrer Erweiterung eine eigene INI-Datei. 3 Stunden 15 Minuten.
keywords: entwickler, modul 8, einstellungen, INI, eZINI, geltungsbereiche, siteaccess, exp:ini, where, set, move, dry-run, probelauf, eigene ini-datei, ezini, developers course, module 8, settings, scopes, own ini file
type: help
---
# Modul 8: Einstellungen und Siteaccesses

Einstellungen bestimmen fast alles, was eine Installation tut, und dieselbe Einstellung kann aus vielen Dateien
kommen. Dieses Modul bringt Entwicklern bei, die Ladereihenfolge zu lesen, den Geltungsbereich zu wählen, in den eine
Einstellung gehört, Einstellungen mit `exp:ini` statt mit einem Editor zu ändern und dem eigenen Code Einstellungen
mit dokumentierten Standardwerten zu geben.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 3 Stunden 15 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 8.1: Wie Einstellungen gelesen werden](wie-einstellungen-gelesen-werden.md) | 45 Minuten | Das Dateiformat, die Reihenfolge der Geltungsbereiche, Arrays und Rücksetzzeilen, `exp:ini where` |
| [Lektion 8.2: Siteaccess-Einstellungen](siteaccess-einstellungen.md) | 45 Minuten | Siteaccesses, Matching, `-s`, den Geltungsbereich einer Einstellung wählen |
| [Lektion 8.3: Einstellungen mit exp:ini ändern](einstellungen-mit-exp-ini-aendern.md) | 50 Minuten | Aktionen, Probeläufe, Sicherungen, Exit-Codes, Verschieben in eine Erweiterung |
| [Lektion 8.4: Eigene Einstellungen](eigene-einstellungen.md) | 45 Minuten | `mysite.ini` mit Standardwerten, aus PHP und Templates gelesen, pro Siteaccess überschrieben |

## Dieses Modul vorbereiten

- Die Entwicklungsinstallationen haben mindestens die Siteaccesses einer Installation mit `exp:install` (eine
  Website, `admin` und `editor`); schreiben Sie ihre Namen an die Tafel.
- Die Teilnehmenden arbeiten als Benutzer der Website, nicht als root: `exp:ini` schreibt Dateien, und
  Einstellungsdateien, die root gehören, legen eine Website lahm.
- Schritt 7 von Lektion 8.3 verschiebt Einstellungen; sagen Sie den Teilnehmenden, dass sie beim Probelauf aufhören,
  sofern der Block nicht ihr eigener ist.
- Prüfen Sie für den Verweis auf den Kurs zu Multisite und Seitenpaketen, dass dessen Modul 1 in Ihrem Hilfe-Center
  verfügbar ist.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 8.1 | 45 |
| Lektion 8.2 | 45 |
| Pause | 10 |
| Lektion 8.3 | 50 |
| Lektion 8.4 | 45 |
| **Gesamt** | **195** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
