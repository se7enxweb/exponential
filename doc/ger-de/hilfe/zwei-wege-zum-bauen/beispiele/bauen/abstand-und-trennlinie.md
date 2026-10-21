---
title: "Abstand und eine Linie zwischen Abschnitten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1111
summary: Die Blöcke Spacer (Height in pixels, Standard 32) und Divider (Style, Margin) oder vertikaler Leerraum an den Blöcken selbst.
keywords: Abstand, Trennlinie, Leerraum, Rand, Linie, Trenner, vertikaler Leerraum, spacer, divider, whitespace, margin, line, separator, vertical whitespace
type: help
---
# Abstand und eine Linie zwischen Abschnitten

Platz zwischen zwei Abschnitten und eine dünne Linie.

*Lebenszyklus: Bauen, Beispiel 12 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Spacer** (Gruppe *Basic*) in die Zone `main`. **Height in pixels** (Standard 32).
3. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Divider** (Gruppe *Basic*) in die Zone `main`. **Style** (Standard `solid`) und **Margin in pixels** (Standard 16).
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Oft besser: **Use vertical whitespace** im Reiter **Design** der Blöcke selbst (On top, On bottom), das die Klassen `whitespace-top-<size>` schreibt, die das Design gestaltet.

## Mit Templates

1. Fügen Sie die Abstände im Stylesheet Ihrer Design-Erweiterung hinzu, an den Klassen der Blöcke ([Ihre Blöcke gestalten](../../bloecke-gestalten.md)).

## Welchen Weg Sie wählen

Abstände sind Design: Ziehen Sie die Leerraum-Option oder CSS vor; den Spacer-Block für einen Einzelfall.

## Prüfen

- Der Abstand ist in Telefonbreite derselbe.

## Verwandte Seiten

- [Der Reiter Design](../../der-reiter-design.md)
- [Ihre Blöcke gestalten](../../bloecke-gestalten.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
