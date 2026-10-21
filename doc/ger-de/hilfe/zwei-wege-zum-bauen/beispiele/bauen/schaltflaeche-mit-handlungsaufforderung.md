---
title: "Eine Schaltfläche mit Handlungsaufforderung"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1105
summary: Der Button / Link-Block (Text, Style, Target, Use link) oder ein Link in einem Template.
keywords: Schaltfläche, Handlungsaufforderung, CTA, Button / Link-Block, Link, Ziel, button, call to action, Button / Link block, link, target
type: help
---
# Eine Schaltfläche mit Handlungsaufforderung

Eine Schaltfläche, die zur Anmeldeseite führt.

*Lebenszyklus: Bauen, Beispiel 6 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Button / Link** (Gruppe *Basic*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Text**, **Style** (Standard `default_button`), **Target** (Standard `_self`) und **Use link** mit dem Link aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Das Media-Design zeichnet ihn mit seinem eigenen `explayouts/block/button.tpl`.

## Mit Templates

1. In einem beliebigen Template Ihrer Design-Erweiterung:

```smarty
<a class="btn btn-primary" href={'/user/register'|ezurl}>{'Sign up'|i18n( 'mysite' )}</a>
```

## Welchen Weg Sie wählen

Schaltflächen, die sich mit Kampagnen ändern: der Block. Eine Schaltfläche, die zum Seitendesign gehört (immer da): das Template.

## Prüfen

- Der Link öffnet die richtige Seite und, mit Target `_blank`, ein neues Fenster.

## Verwandte Seiten

- [Der Reiter Design](../../der-reiter-design.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
