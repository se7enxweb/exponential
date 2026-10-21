---
title: Performance von Themes
audience: designer
category: theme-design
order: 650
summary: Was ein Design schnell oder langsam macht: Zahl der Anfragen und Größe von CSS und JS, Dateien, die das Rendern blockieren, Schriften, Bilder, fetch in Templates, Cache-Blöcke und der View-Cache, mit einem kurzen Budget und wie Sie messen.
keywords: Performance, schnelles Theme, Seitengeschwindigkeit, Rendern blockieren, kritisches CSS, Skripte verzögern, Schriften, Bilder, Performance von fetch, cache-block, Core Web Vitals, LCP, CLS, Budget, performance, fast theme, page speed, render blocking, critical css, defer scripts, fonts, images, fetch performance, budget
type: help
---
# Performance von Themes

Diese Seite ist für Designer, die ein Design möchten, das sich sofort anfühlt. Über die Performance wird an zwei Stellen entschieden: im
Browser (was er laden und zeichnen muss) und auf dem Server (was Templates holen und rendern). Die Seite listet die Hebel auf beiden Seiten.

## Inhalt

1. [Im Browser](#im-browser)
2. [Auf dem Server](#auf-dem-server)
3. [Ein einfaches Budget](#ein-einfaches-budget)
4. [Messen](#messen)

## Im Browser

| Hebel | Wie |
|---|---|
| Weniger Anfragen | ein `ezcss_load` und ein oder zwei Skriptbündel, gepackt ([Der Packer](der-packer.md)) |
| Weniger Blockieren des Renderns | Stylesheets im Kopf, Skripte am Ende des Body oder mit `defer` (das Design media trennt kritische und verzögerte Skripte) |
| Schriften | selbst gehostet, `font-display: swap`, wenige Schnitte, Teilmengen mit `unicode-range` ([Schriften in einem Design](schriften-in-einem-design.md)) |
| Bilder | Aliase in der angezeigten Größe, `srcset`, `loading="lazy"` unterhalb des ersten Bildschirms, Breite und Höhe gesetzt ([Bild-Performance](bildleistung.md)) |
| Standardmäßig keine Drittanbieter | [Externe Ressourcen und CDNs](externe-ressourcen-und-cdns.md) |
| Seitenspezifisches CSS und JS nur, wo verwendet | `ezcss_require` / `ezscript_require` aus dem View-Template |
| Stabiles Layout | reservierter Platz für Bilder, Anzeigen, Einbettungen ([CLS und LCP messen](cls-und-lcp-messen.md)) |

## Auf dem Server

| Hebel | Wie |
|---|---|
| Der View-Cache | halten Sie Vollansichten zwischenspeicherbar; nichts Benutzerspezifisches darin ([Caching für Designer](caching-fuer-designer.md)) |
| Cache-Blöcke | schließen Sie Menüs und Fußbereiche mit den richtigen Schlüsseln ein |
| Weniger fetch-Aufrufe | einmal im Pagelayout holen, das Ergebnis an Teile übergeben; Werte aus `content_info` vorziehen ([Performance von fetch](../vorlagen/fetch-leistung.md)) |
| Limits | immer ein `limit` bei Listen-fetch; blättern |
| Kein fetch in einer Schleife | die Kinder einmal holen, nicht ein fetch pro Eintrag |

## Ein einfaches Budget

Für eine öffentliche Inhaltsseite peilen Sie an:

- eine Stylesheet-Anfrage und höchstens zwei Skript-Anfragen vom Design;
- keine Anfrage an einen fremden Host vor der Einwilligung;
- das größte Bild des ersten Bildschirms in einem Alias nahe seiner angezeigten Größe ausgeliefert;
- Layoutverschiebung (CLS) höchstens 0,05, das Ziel, das das Projekt für seine eigenen Seiten verwendet.

## Messen

- Der Netzwerkbereich des Browsers: Zählen Sie Anfragen, prüfen Sie Größen, suchen Sie Dateien, die das Rendern blockieren.
- Der Bereich Performance oder Lighthouse des Browsers für Largest Contentful Paint und Cumulative Layout Shift.
- Auf dem Server zeigt die Debug-Ausgabe (`DebugOutput=enabled` für Ihre Adresse) Zeiten und die Zahl der Datenbankabfragen einer Seite;
  Templates mit vielen fetch-Aufrufen fallen dort auf.
- Messen Sie abgemeldet und mit kaltem und warmem Cache: Der erste Besucher nach einer Veröffentlichung sieht die nicht zwischengespeicherte
  Seite.

## Verwandte Seiten

- [Bild-Performance](bildleistung.md)
- [CLS und LCP messen](cls-und-lcp-messen.md)
- [Checkliste: Performance](checkliste-leistung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
