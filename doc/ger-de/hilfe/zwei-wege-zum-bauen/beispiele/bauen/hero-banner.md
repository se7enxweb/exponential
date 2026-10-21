---
title: "Ein Hero-Banner oben auf einer Seite"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1100
summary: Ein großes Bild mit Titel, einem Satz und einer Schaltfläche: der Hero-Block mit seinen sechs Feldern oder ein Template-Block mit demselben Markup.
keywords: Hero, Banner, Hero-Block, Bild, Handlungsaufforderung, Schaltfläche, Landingpage, Template-Block, hero, banner, Hero block, image, call to action, button, landing page, template block
type: help
---
# Ein Hero-Banner oben auf einer Seite

Ein Hero-Banner eröffnet eine Landingpage: ein Bild, ein Titel, ein Satz und eine Schaltfläche zum nächsten Schritt.

*Lebenszyklus: Bauen, Beispiel 1 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Hero** (Gruppe *Components*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Title**, **Content**, **Image URL**, **Image alt text**, **Link text** und **Link URL** aus; **CSS class** und **CSS ID** sind optional.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Der Hero wird mit `explayouts/block/hero.tpl` des Standard-Designs von Layouts dargestellt. Der vollständige Durchgang mit Bildschirmfotos ist [Eine Kampagnenseite bauen, von Anfang bis Ende](../../kampagnenseite-bauen.md).

## Mit Templates

1. Schreiben Sie `extension/mysite/design/media/templates/explayouts/tpl_block/hero_spring.tpl` in Ihrer Design-Erweiterung, leeren Sie den Template-Cache und platzieren Sie es mit einem **Template block**, dessen **Template name** `hero_spring` ist ([Eigene Template-Blöcke](../../eigene-template-bloecke.md)).
2. Verbinden Sie Bild und Text mit Inhalten, statt sie einzutippen: Holen Sie den Knoten und geben Sie seine Felder aus.

```smarty
{def $hero = fetch( 'content', 'node', hash( 'node_id', 92 ) )}
<section class="container hero-spring">
    <h2>{$hero.name|wash}</h2>
    {attribute_view_gui attribute=$hero.data_map.full_intro}
    <a class="btn" href={$hero.url_alias|ezurl}>{'See the plans'|i18n( 'mysite' )}</a>
</section>
{undef $hero}
```

## Welchen Weg Sie wählen

Redakteure ändern Kampagnen-Heros oft: Verwenden Sie den Hero-Block. Verwenden Sie das Template, wenn der Hero immer ein bestimmtes Inhaltsobjekt zeigen und dessen Änderungen folgen soll.

## Prüfen

- Der Quelltext der Seite enthält `<div class="ngl-block ngl-hero ...">` (Block) oder Ihren Abschnitt `hero-spring` (Template).
- Das Bild hat den Alternativtext, den Sie eingegeben haben.

## Verwandte Seiten

- [Eine Kampagnenseite bauen, von Anfang bis Ende](../../kampagnenseite-bauen.md)
- [Eigene Template-Blöcke](../../eigene-template-bloecke.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
