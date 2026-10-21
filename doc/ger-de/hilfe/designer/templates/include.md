---
title: Include
audience: designer, developer
category: templates
order: 100
summary: Templates mit include in Teile aufteilen - die Ressource design, Parameter übergeben, was das eingebundene Template sieht, Teile benennen und include gegenüber node_view_gui und Overrides.
keywords: include, include uri, Ressource design, Template-Teile, Teil-Template, Parameter, Variablen übergeben, Template wiederverwenden, Unter-Template, design resource, template parts, partial template, parameters, pass variables, reuse template, sub template
type: help
---
# Include

Diese Seite ist für Designer, die Templates in wiederverwendbare Teile aufteilen: einen Kopfbereich, einen Teaser, eine
Leiste zum Teilen.

## Inhalt

1. [Syntax](#syntax)
2. [Parameter übergeben](#parameter-übergeben)
3. [Was das eingebundene Template sieht](#was-das-eingebundene-template-sieht)
4. [Dynamische Pfade](#dynamische-pfade)
5. [Include oder node_view_gui?](#include-oder-node_view_gui)

## Syntax

```tpl
{include uri='design:pagelayout/header.tpl'}
```

`design:` bedeutet „diesen Pfad unter `templates/` über die Design-Kaskade finden“, sodass ein Design weiter oben in der
Kette den Teil ersetzen kann, indem es eine Datei an denselben Pfad legt ([Die Design-Kaskade](../../../../eng-us/help/designers/theme-design/the-design-cascade.md)).

## Parameter übergeben

Jedes weitere `name=value` wird zu einer Variablen im eingebundenen Template:

```tpl
{include uri='design:parts/teaser.tpl' node=$child show_image=true() image_class='small'}
```

Geben Sie optionalen Parametern im Teil einen Standardwert:

```tpl
{* design/mytheme/templates/parts/teaser.tpl *}
{default show_image=false() image_class='medium'}
<article class="teaser">
    {if and( $show_image, $node.data_map.image.has_content )}
        {attribute_view_gui attribute=$node.data_map.image image_class=$image_class}
    {/if}
    <h3><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h3>
</article>
```

## Was das eingebundene Template sieht

Der Teil sieht die Variablen seines Einbindenden plus die Parameter. Variablen, die er mit `{def}` definiert, bleiben bis
`{undef}` im gemeinsamen Namensraum; deshalb sollten Teile mit `{undef}` entfernen, was sie definieren, und Namen mit Präfix
verwenden ([Variablen und def](variablen-und-def.md)).

## Dynamische Pfade

Der Pfad lässt sich zusammensetzen:

```tpl
{include uri=concat( 'design:parts/icon_', $type, '.tpl' )}
```

Die Layout-Engine bindet Block-Templates auf diese Weise ein (`concat( 'design:explayouts/block/', $block.definition_identifier, '.tpl' )`).
Ein Pfad, der nicht existiert, ist ein Template-Fehler: Stellen Sie sicher, dass jeder mögliche Wert eine Datei hat.

## Include oder node_view_gui?

| Verwenden | Wann |
|---|---|
| `{include}` | ein Teil des Rahmens oder Markup, das nicht von der Inhaltsklasse abhängt |
| `{node_view_gui content_node=$child view='line'}` | einen Knoten zeichnen: Das Template des Ansichtsmodus wird über Overrides pro Klasse gewählt ([node_view_gui](node-view-gui.md)) |

## Verwandte Seiten

- [Variablen und def](variablen-und-def.md)
- [Das Pagelayout](../../../../eng-us/help/designers/theme-design/the-pagelayout.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
