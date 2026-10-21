---
title: "Der Lebenszyklus eines Themes"
audience: designer, project-manager
category: theme-lifecycle
order: 20
summary: Die Phasen, die ein Theme durchläuft - Entwicklung, erstes Release, aktive Wartung, reine Sicherheitswartung, Lebensende, Archiv oder Übergabe -, was jede Phase Nutzern verspricht und wie Sie einen Phasenwechsel ankündigen.
keywords: Lebenszyklus, Phasen, aktive Wartung, nur Sicherheit, Lebensende, Archiv, Übergabe, Support-Status, life cycle, stages, active maintenance, security only, end of life, archive, handover, support status
type: help
---
# Der Lebenszyklus eines Themes

Diese Seite ist für Designer, die planen, wie lange und wie intensiv sie ein Theme pflegen. Nutzer planen ihre Websites
um Ihr Theme herum; ihnen zu sagen, in welcher Phase es ist, gehört zur Arbeit.

## Die Phasen

| Phase | Sie tun | Nutzer können erwarten |
|---|---|---|
| Entwicklung | Bauen, kein stabiles Release | Noch nichts; nur `dev-main` |
| Aktive Wartung | Neue Funktionen, Kompatibilität, Korrekturen, Prüfungen | Regelmäßige Releases |
| Reine Sicherheitswartung | Sicherheitskorrekturen und kritische Kompatibilitätskorrekturen | Releases nur bei Bedarf |
| Lebensende | Nichts Neues | Das letzte Release bleibt installierbar |
| Archiviert oder übergeben | Das Repository ist nur lesbar, oder jemand anderes pflegt es | Ein Hinweis auf den Nachfolger, falls es einen gibt |

## Schritt für Schritt: die Phase angeben

1. Fügen Sie oben im README eine Zeile „Status“ hinzu: „Status: aktiv gepflegt“ (oder eine andere Phase).
2. Halten Sie die Support-Richtlinie aktuell ([Eine Support-Richtlinie für Ihr Theme](support-richtlinie-fuer-ihr-theme.md)).
3. Wenn sich die Phase ändert, sagen Sie es in einer Release-Notiz, im README und auf der Projektseite auf
   projects.exponential.earth ([Das Lebensende ankündigen](das-lebensende-ankuendigen.md)).

## Echte Beispiele

Die Themes, die 7x pflegt, sind in aktiver Wartung: Ihre Release-Listen zeigen im Oktober 2026 Releases in dichter Folge,
zum Beispiel `cjw_themes_jumper` 0.1.2 bis 0.1.6 zwischen dem 11. und 17. Oktober, mit Open-Graph-Tags, einer
Sicherheitskorrektur, Barrierefreiheit und der Entfernung von Inline-Skripten.

## Verwandte Seiten

- [Lebensende](lebensende.md)
- [Ein Theme übergeben](uebergabe-an-einen-neuen-betreuer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
