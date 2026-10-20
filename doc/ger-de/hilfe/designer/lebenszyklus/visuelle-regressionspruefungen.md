---
title: "Visuelle Regressionsprüfungen: hell, dunkel, Zoom"
audience: designer
category: theme-lifecycle
order: 160
summary: Unbeabsichtigte optische Änderungen zwischen Releases erkennen - Screenshots der Seitenliste bei festen Breiten und Modi vorher und nachher, nebeneinander oder per Werkzeug verglichen - mit den Breiten, die das Projekt Exponential verwendet.
keywords: visuelle Regression, Screenshots, vergleichen, heller Modus, Dunkelmodus, Zoom, 1440, 960, 375, vorher nachher, visual regression, compare, light mode, dark mode, before after
type: help
---
# Visuelle Regressionsprüfungen: hell, dunkel, Zoom

Diese Seite ist für Designer, die wissen wollen, was ein Release optisch geändert hat, auch das, was sie nicht
beabsichtigt haben.

## Die Matrix

| Breite | Skalierung | Modi |
|---|---|---|
| 1440 px | 1 | hell, dunkel |
| 960 px | 2 (200 % Zoom) | hell, dunkel |
| 375 px | 2 | hell |

Mit diesen Breiten prüft das Projekt Exponential seine eigenen Seiten.

## Schritt für Schritt

1. Nehmen Sie auf der Testinstallation mit dem **vorigen** Release Screenshots jeder Seite der Seitenliste in jeder Zelle
   der Matrix auf. Benennen Sie sie `<page>-<width>-<mode>.png`.
2. Aktualisieren Sie auf das **neue** Release und nehmen Sie denselben Satz auf.
3. Vergleichen Sie: nebeneinander oder mit einem Bildvergleichswerkzeug, das geänderte Pixel hervorhebt.
4. Entscheiden Sie bei jedem Unterschied: beabsichtigt (im Changelog vermerken) oder eine Regression (beheben).
5. Behalten Sie den neuen Satz als Grundlage für das nächste Release, außerhalb der veröffentlichten Dateien des Themes,
   wenn er groß ist.

## Typische Regressionen

- Dunkelmodus: eine weiße Karte auf dunklem Hintergrund, Text mit zu wenig Kontrast.
- Zoom: ein Kopfbereich, der Inhalte überdeckt, waagerechtes Scrollen.
- Telefon: ein Menü, das sich nicht öffnet, Bilder, die überlaufen.

## Verwandte Seiten

- [Screenshots für ein Theme](../veroeffentlichung/screenshots-fuer-ein-theme.md)
- [Checkliste: Dunkelmodus](../../../../eng-us/help/designers/theme-design/checklist-dark-mode.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
