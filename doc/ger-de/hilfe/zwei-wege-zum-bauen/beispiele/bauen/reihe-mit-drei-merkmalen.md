---
title: "Eine Reihe mit drei Merkmalen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1102
summary: Drei kurze Verkaufsargumente: der Features-Block, ein Three-columns-Container mit drei Cards oder ein Template.
keywords: Merkmale, Verkaufsargumente, Features-Block, drei Spalten, Karten, Komponenten, features, selling points, Features block, three columns, cards, components
type: help
---
# Eine Reihe mit drei Merkmalen

Drei kurze Punkte nebeneinander: „Plans for every level“, „Recipes“, „Videos“.

*Lebenszyklus: Bauen, Beispiel 3 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Three columns** (Gruppe *Containers*) in die Zone `main`. Seine Platzhalter sind `col_1`, `col_2`, `col_3`.
3. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Card** (Gruppe *Basic*) in die Zone `col_1`. Machen Sie dasselbe für `col_2` und `col_3`.
4. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie bei jeder Card **Title**, **Content**, **Image URL**, **Link URL** und **Link label** (Standard „Read more“) aus.
5. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Der Block **Features** (Gruppe *Components*) ist die Alternative aus einem einzigen Block mit den gemeinsamen Komponentenfeldern.

## Mit Templates

1. Schreiben Sie `extension/mysite/design/media/templates/explayouts/tpl_block/features_three.tpl` in Ihrer Design-Erweiterung, leeren Sie den Template-Cache und platzieren Sie es mit einem **Template block**, dessen **Template name** `features_three` ist ([Eigene Template-Blöcke](../../../../../eng-us/help/build-two-ways/template-blocks.md)).
2. Gehen Sie in einer Schleife über drei Inhaltsobjekte (zum Beispiel die Kinder eines Ordners „Features“).

```smarty
{def $features = fetch( 'content', 'list', hash( 'parent_node_id', <folder node id>, 'limit', 3, 'sort_by', array( 'priority', true() ) ) )}
<div class="container features-three">
{foreach $features as $f}
    <div class="feature"><h3>{$f.name|wash}</h3>{attribute_view_gui attribute=$f.data_map.short_description}</div>
{/foreach}
</div>
{undef $features}
```

## Welchen Weg Sie wählen

Mit Spalten und Cards kann ein Redakteur eine Card ändern, ohne die anderen anzufassen; mit einem Template, das aus einem Ordner liest, ändern Redakteure die Punkte als Inhalte.

## Prüfen

- Drei Blöcke `ngl-card` innerhalb von `ngl-three_columns` (Weg über Blöcke).

## Verwandte Seiten

- [Der Reiter Design](../../../../../eng-us/help/build-two-ways/design-tab-options.md)
- [Eigene Template-Blöcke](../../../../../eng-us/help/build-two-ways/template-blocks.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
