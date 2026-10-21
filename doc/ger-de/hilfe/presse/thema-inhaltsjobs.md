---
title: "Thema: Inhaltsjobs"
audience: press
category: press-media
order: 87
summary: Ein Themenbeitrag über Inhaltsjobs in der Produktlinie 6.0.15 von Exponential - große Lösch-, Kopier- und Verschiebevorgänge laufen im Hintergrund in Stapeln mit Prüfpunkten und setzen nach einem Absturz fort, und dieselbe Engine installiert Pakete und schreibt große Audit-Exporte - mit Fakten, Quellen und Aussagen, die Sie vermeiden sollten.
keywords: Thema, Inhaltsjobs, Hintergrundjobs, Stapel, Prüfpunkt, fortsetzen, große Vorgänge, Teilbaum, verschieben, kopieren, entfernen, Pakete, Zuverlässigkeit, story, content jobs, background jobs, batch, checkpoint, resume, large operations, subtree, move, copy, remove, packages, reliability
type: help
---
# Thema: Inhaltsjobs

Diese Seite ist für technische Journalisten, die über Zuverlässigkeit schreiben. Inhaltsjobs lösen ein altes Problem von
Webanwendungen: einen Vorgang, der zu groß ist, um innerhalb einer einzigen Webanfrage fertig zu werden.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
3. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
4. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

Das Entfernen, Kopieren oder Verschieben eines großen Teils des Inhaltsbaums einer Website „lief früher innerhalb einer
einzigen Webanfrage und konnte am Zeitlimit, am Speicherlimit oder an einer riesigen Transaktion scheitern“. Ab der
Produktlinie 6.0.15 bietet die Administration an, einen solchen Vorgang als **Inhaltsjob** auszuführen: „ein
Hintergrundprozess, der in Stapeln arbeitet, nach jedem Stapel einen Prüfpunkt setzt und genau dort weitermacht, wo er
aufgehört hat, sogar nach einem `kill -9`“ ([Inhaltsjobs](../../../eng-us/features/6.0/content-jobs.md)). Seit dem
12. Oktober 2026 installiert dieselbe Engine auch Pakete im Hintergrund und schreibt große Audit-Exporte in eine Datei.

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Inhaltsjobs wurden am 2. Oktober 2026 hinzugefügt | [Inhaltsjobs](../../../eng-us/features/6.0/content-jobs.md) |
| Stapel, ein Prüfpunkt nach jedem Stapel, Fortsetzen nach einer Unterbrechung | ebenda |
| Jobs lassen sich in der Administration und auf der Kommandozeile starten und verfolgen | [Von der Kommandozeile](../../../eng-us/features/6.0/content-jobs.md#from-the-command-line) |
| Paketinstallation und Audit-Exporte verwenden dieselbe Engine (seit 12. Oktober 2026) | [Inhaltsjobs](../../../eng-us/features/6.0/content-jobs.md) |
| Die eingereihte Arbeit von Erweiterungen (zum Beispiel Exponential Intelligence) läuft in einer eigenen Job-Warteschlange, `expqueue` | [expqueue](../../../eng-us/features/6.0/extensions/expqueue.md) |
| Entwickler können eigene Jobtypen hinzufügen | [Inhaltsjobs (Entwickler)](../../../eng-us/bc/6.0/content-jobs.md) |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Vorgänge jeder Größe sind sofort fertig“ | Sie laufen im Hintergrund, in Stapeln |
| „Erschienen“ | In der Produktlinie 6.0.15, in Entwicklung |

## Blickwinkel und Ideen für Überschriften

- „Wenn ein Klick eine Stunde dauern würde: Hintergrundjobs für große Inhaltsänderungen.“
- „Absturzsichere Inhaltsvorgänge in einem Open-Source-CMS.“

## Verwandte Seiten

- [Thema: Betriebswerkzeuge für Administratoren](thema-betriebswerkzeuge-fuer-administratoren.md)
- [Thema: der Audit-Trail](thema-audit-trail.md)
- [Funktionsthemen: Übersicht](../../../eng-us/help/press/feature-stories-index.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
