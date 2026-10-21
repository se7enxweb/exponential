---
title: "Checkliste: Performance"
audience: designer
category: theme-design
order: 880
summary: Eine Performance-Checkliste für ein Design: Anfragen und Bündel, Blockieren des Renderns, Schriften, Bilder, Layoutverschiebung, Drittanbieter, fetch in Templates, View-Cache und Cache-Blöcke, gemessen abgemeldet mit kalten und warmen Caches.
keywords: Performance-Checkliste, Geschwindigkeits-Checkliste, Seitengeschwindigkeit, CLS, LCP, Anfragen, Bündel, Bildgrößen, fetch-Limits, Schlüssel von Cache-Blöcken, Performance-Prüfung eines Designs, performance checklist, speed checklist, page speed, requests, bundles, image sizes, fetch limits, cache-block keys, design performance review
type: help
---
# Checkliste: Performance

Diese Seite ist für Designer, die die Geschwindigkeit eines Designs prüfen. Messen Sie abgemeldet, auf der Startseite, einem
Artikel und einer Liste, einmal direkt nach dem Leeren der Caches und noch einmal.

## Inhalt

1. [Im Browser](#im-browser)
2. [Auf dem Server](#auf-dem-server)
3. [Zahlen, die Sie festhalten](#zahlen-die-sie-festhalten)

## Im Browser

- [ ] Eine Stylesheet-Anfrage vom Design (gepackt) ([Der Packer](../../../../eng-us/help/designers/theme-design/the-packer.md)).
- [ ] Höchstens zwei Skript-Anfragen; nicht kritische Skripte verzögert ([JavaScript laden](../../../../eng-us/help/designers/theme-design/loading-javascript.md)).
- [ ] Kein Skript im Kopf, das das Rendern blockiert, außer einem winzigen Schutz vor dem ersten Zeichnen.
- [ ] Schriften selbst gehostet, `font-display: swap`, wenige Schnitte ([Schriften in einem Design](schriften-in-einem-design.md)).
- [ ] Bilder in Aliassen nahe der angezeigten Größe, `srcset`, wo Größen variieren ([Bild-Performance](bildleistung.md)).
- [ ] Bilder unterhalb des ersten Bildschirms lazy; das Hauptbild nicht lazy.
- [ ] Breite und Höhe an Bildern und Einbettungen; CLS höchstens 0,05 ([CLS und LCP messen](../../../../eng-us/help/designers/theme-design/measuring-cls-and-lcp.md)).
- [ ] Keine Anfrage an Drittanbieter vor der Einwilligung ([Externe Ressourcen und CDNs](externe-ressourcen-und-cdns.md)).
- [ ] Seitenspezifisches CSS und JS nur von den Templates angefordert, die es brauchen.

## Auf dem Server

- [ ] Vollansichten liegen im View-Cache; nichts Benutzerspezifisches darin ([Caching für Designer](caching-fuer-designer.md)).
- [ ] Menüs und Fußbereiche in Cache-Blöcken mit korrekten, kleinen Schlüsseln.
- [ ] Jedes Listen-fetch hat ein `limit`; kein fetch innerhalb eines `foreach` ([Performance von fetch](../templates/fetch-leistung.md)).
- [ ] Der aktuelle Knoten wird einmal im Pagelayout geholt und von den Teilen wiederverwendet.
- [ ] Die Debug-Ausgabe (auf einem Test-Siteaccess) zeigt eine vernünftige Zahl von Datenbankabfragen für die Seite.

## Zahlen, die Sie festhalten

| Seite | Anfragen | Übertragungsgröße | LCP | CLS | Abfragen (kalt) |
|---|---|---|---|---|---|
| Startseite | | | | | |
| Artikel | | | | | |
| Liste | | | | | |

Halten Sie sie beim Start fest; vergleichen Sie nach jeder Veröffentlichung des Designs.

## Verwandte Seiten

- [Performance von Themes](../../../../eng-us/help/designers/theme-design/theme-performance.md)
- [Checkliste: Start eines Themes](checkliste-theme-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
