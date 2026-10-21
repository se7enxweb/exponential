---
title: "Druckstile für Artikel"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1429
summary: Druckregeln gehören in Ihr Stylesheet (@media print), für die Klassen, die die Templates schreiben.
keywords: Druck, @media print, Stylesheet, Artikel, Neugestaltung, print, stylesheet, articles, redesign
type: help
---
# Druckstile für Artikel

Artikel werden ohne Kopfzeile, Fußzeile und Teaser gedruckt.

*Lebenszyklus: Neugestaltung, Beispiel 30 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Keine Änderung am Layout.

## Mit Templates

1. In `mysite.css`: `@media print { .site-header, .site-footer, .zone-pre_footer { display: none; } }`; die Klassen kommen aus `pagelayout/header.tpl`, `pagelayout/footer.tpl` und `explayouts/zone.tpl`.

## Welchen Weg Sie wählen

CSS.

## Prüfen

- Die Druckvorschau zeigt nur den Artikel.

## Verwandte Seiten

- [Ihre Blöcke gestalten](../../bloecke-gestalten.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
