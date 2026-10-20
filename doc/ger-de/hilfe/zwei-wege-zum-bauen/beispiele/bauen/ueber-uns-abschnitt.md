---
title: "Ein Abschnitt „Über uns“ mit Bild und Text"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1101
summary: Titel, Text, Bild und Link nebeneinander: der About-Block oder dasselbe Markup in einem Template-Block.
keywords: Über uns, About-Block, Bild und Text, Unternehmen, Team, Komponenten, about, about us, About block, image and text, company, team, components
type: help
---
# Ein Abschnitt „Über uns“ mit Bild und Text

Ein Band „Über uns“: wer Sie sind, in zwei Sätzen, ein Bild und ein Link zur vollständigen Seite.

*Lebenszyklus: Bauen, Beispiel 2 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **About** (Gruppe *Components*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Title**, **Content**, **Image URL**, **Image alt text**, **Link text** und **Link URL** aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

About, Hero, Features und Lead teilen sich einen Handler (`expLayoutsComponentBlockHandler`) und damit dieselben acht Felder; jeder Typ hat sein eigenes Template.

## Mit Templates

1. Schreiben Sie `extension/mysite/design/media/templates/explayouts/tpl_block/about_us.tpl` in Ihrer Design-Erweiterung, leeren Sie den Template-Cache und platzieren Sie es mit einem **Template block**, dessen **Template name** `about_us` ist ([Eigene Template-Blöcke](../../../../../eng-us/help/build-two-ways/template-blocks.md)).
2. Lesen Sie den Text aus einer Inhaltsseite, damit er dort bearbeitet wird, wo er hingehört, zum Beispiel aus einer `ng_landing_page` „About us“ mit `title`, `full_intro` und `image`.

```smarty
{def $about = fetch( 'content', 'node', hash( 'node_id', <node id of About us> ) )}
<section class="container about-us">
    <h2>{$about.name|wash}</h2>
    {attribute_view_gui attribute=$about.data_map.full_intro}
    {attribute_view_gui attribute=$about.data_map.image}
</section>
{undef $about}
```

## Welchen Weg Sie wählen

Ein fester Text, der sich selten ändert, passt auf beide Arten; der Block hält ihn beim Layout, das Template hält ihn in git.

## Prüfen

- `ngl-about` am Wrapper (Block).
- Der Link führt zur vollständigen Seite.

## Verwandte Seiten

- [Eigene Template-Blöcke](../../../../../eng-us/help/build-two-ways/template-blocks.md)
- [Der Reiter Design](../../../../../eng-us/help/build-two-ways/design-tab-options.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
