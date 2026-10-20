---
title: "Eine Leistungsprüfung bei jedem Release"
audience: designer, developer
category: theme-lifecycle
order: 170
summary: Prüfen, dass ein Release das Theme nicht langsamer gemacht hat - Seitengewicht, Zahl der Anfragen, Bildgrößen, Layoutverschiebung und Largest Contentful Paint auf der Seitenliste - und mit dem vorigen Release vergleichen.
keywords: Leistungsprüfung, Seitengewicht, Anfragen, Bilder, CLS, LCP, Lighthouse, Packer, Release-Routine, performance audit, page weight, requests, images, release routine
type: help
---
# Eine Leistungsprüfung bei jedem Release

Diese Seite ist für Designer, die ein Theme betreuen. Die Leistung verschlechtert sich von Release zu Release, wenn sie
nicht gemessen wird. Wie Sie ein schnelles Theme bauen, steht in [Theme-Leistung](../../../../eng-us/help/designers/theme-design/theme-performance.md)
und [CLS und LCP messen](../../../../eng-us/help/designers/theme-design/measuring-cls-and-lcp.md).

## Was Sie messen

| Messgröße | Wie | Achten Sie auf |
|---|---|---|
| Seitengewicht | Entwicklerwerkzeuge des Browsers, Netzwerk-Bereich | Wachstum ohne Grund |
| Anfragen | Dasselbe | Neue Schriften, Skripte, Bilder |
| Bildgrößen | Dasselbe | Bilder, die größer sind als angezeigt |
| Layoutverschiebung (CLS) | Ein Laborwerkzeug wie Lighthouse | Bilder ohne Abmessungen, späte Schriften |
| Largest Contentful Paint (LCP) | Dasselbe | Große Titelbilder, rendering-blockierende Dateien |

Messen Sie auf der Testinstallation mit aktivem Packer, damit Stylesheets und Skripte gebündelt werden wie auf einer
Live-Website.

## Schritt für Schritt

1. Messen Sie die Startseite, einen Artikel und eine Liste mit dem vorigen Release; schreiben Sie die Zahlen auf.
2. Aktualisieren Sie und messen Sie dieselben Seiten auf dieselbe Weise.
3. Vergleichen Sie. Erklären oder beheben Sie jeden Anstieg.
4. Vermerken Sie nennenswerte Verbesserungen im Changelog.

## Verwandte Seiten

- [Bildleistung](../../../../eng-us/help/designers/theme-design/image-performance.md)
- [Benchmarking](../../../anleitungen/benchmarking.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
