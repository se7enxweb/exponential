---
title: Zeilenansicht
audience: designer, developer
category: templates
order: 520
summary: Die Zeilenansicht, das Template eines Inhaltseintrags in einer Liste - was es erhält, ein Teaser-Beispiel mit Bild, Titel, Datum und Einleitung, Parameter vom Aufrufer, Überschriften innerhalb von Listen und Zeilenansichten pro Klasse.
keywords: Zeilenansicht, node/view/line.tpl, Listeneintrag, Teaser, Karte, Listen-Template, node_view_gui line, Override der Zeilenansicht, Eintrags-Template, line view, list item, teaser, card, list template, line override, item template
type: help
---
# Zeilenansicht

Diese Seite ist für Designer, die Einträge in Listen gestalten: Kinder eines Ordners, Suchergebnisse, neueste Nachrichten.
Der Ansichtsmodus `line` zeichnet einen Eintrag; [node_view_gui](node-view-gui.md) ruft ihn für jeden Knoten auf.

## Inhalt

1. [Was sie erhält](#was-sie-erhält)
2. [Ein Teaser-Beispiel](#ein-teaser-beispiel)
3. [Überschriften und Links](#überschriften-und-links)
4. [Pro Klasse](#pro-klasse)

## Was sie erhält

`$node`, `$view_parameters` (offset 0) und jeden Parameter, den der Aufrufer übergibt
(`{node_view_gui content_node=$child view='line' image_class='small'}`). Das Standard-`design/standard/templates/node/view/line.tpl`
des Kernels gibt ein Icon und den verlinkten Namen aus.

## Ein Teaser-Beispiel

```tpl
{* override/templates/line/article.tpl *}
{default image_class='small' heading='h3'}
<article class="teaser">
    {if $node.data_map.image.has_content}
        <div class="teaser-media">{attribute_view_gui attribute=$node.data_map.image image_class=$image_class}</div>
    {/if}
    <{$heading} class="teaser-title"><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></{$heading}>
    <p class="teaser-meta"><time datetime="{$node.object.published|datetime( 'custom', '%Y-%m-%d' )}">{$node.object.published|l10n( 'shortdate' )}</time></p>
    {if $node.data_map.intro.has_content}
        <div class="teaser-intro">{$node.data_map.intro.content.output.output_text|strip_tags|shorten( 160 )|wash}</div>
    {/if}
</article>
```

## Überschriften und Links

- Die Überschriftenebene hängt davon ab, wo die Liste steht: Ein Listentitel `h2` bedeutet Einträge `h3`. Lassen Sie den
  Aufrufer sie übergeben (`heading` oben).
- Ein Link pro Eintrag, auf dem Titel; eine Karte, deren ganze Fläche anklickbar ist, kann den Titel-Link mit CSS
  ausdehnen, statt alles in `<a>` zu packen.

## Pro Klasse

Ein Override mit `Source=node/view/line.tpl` und `Match[class_identifier]=article` gibt Artikeln ihr Zeilen-Template
([Knoten-Templates nach Klasse](knoten-templates-nach-klasse.md)). Halten Sie das Markup aller Zeilen-Templates ähnlich, damit
Listen mit gemischten Klassen einheitlich aussehen.

## Verwandte Seiten

- [Ansichtsmodi](ansichtsmodi.md)
- [node_view_gui](node-view-gui.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
