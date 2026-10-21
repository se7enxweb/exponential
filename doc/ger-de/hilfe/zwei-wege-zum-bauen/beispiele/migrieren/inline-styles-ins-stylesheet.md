---
title: "Inline-Styles ins Stylesheet verschieben"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1528
summary: style="" und <style> in alten Templates kommen in Ihr Stylesheet und werden mit dem des Themes gepackt.
keywords: Inline-Styles, Stylesheet, Aufräumen, CSP, Migration, inline styles, stylesheet, cleanup, migration
type: help
---
# Inline-Styles ins Stylesheet verschieben

Templates ohne Inline-CSS, Styles im Cache.

*Lebenszyklus: Migrieren, Beispiel 29 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Blöcke mit eingetippten Styles in HTML-Snippets: Ersetzen Sie auch diese.

## Mit Templates

1. Ersetzen Sie jedes `style=""` durch eine Klasse; fügen Sie die Regeln zu `mysite.css` hinzu; nehmen Sie die Datei über Ihren Override von `pagelayout/head/style.tpl` in die Liste des Themes auf.

## Welchen Weg Sie wählen

Ein Stylesheet, kein Inline-CSS.

## Prüfen

- In Ihren Templates ist kein `style=` mehr übrig.

## Verwandte Seiten

- [Ihre Blöcke gestalten](../../bloecke-gestalten.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
