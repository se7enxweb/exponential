---
title: Eigene Ansichtsmodi
audience: designer, developer
category: templates
order: 550
summary: Eigene Ansichtsmodi hinzufügen (card, teaser, hero, payload) - ein Standard-Template node/view für den Modus, Klassen-Overrides dafür, die Verwendung über node_view_gui und Layout-Blöcke, der Aufruf per URL, Caching und die Auswahl der Redakteure beim Einbetten.
keywords: eigener Ansichtsmodus, neuer Ansichtsmodus, Kartenansicht, Teaser-Ansicht, eigene node/view, Template eines Ansichtsmodus, payload-Ansicht, Ansichtsmodus für Blöcke, AvailableViewModes, custom view mode, new view mode, card view, teaser view, view mode template, view mode for blocks
type: help
---
# Eigene Ansichtsmodi

Diese Seite ist für Designer, die mehr Formen brauchen als `full`, `line` und `embed`: eine Karte in einem Raster, einen
großen Teaser in einem Titelbereich, einen kompakten Eintrag in einer Seitenleiste. Ein Ansichtsmodus ist nur ein Name mit
Templates dahinter.

## Inhalt

1. [Einen Ansichtsmodus anlegen](#einen-ansichtsmodus-anlegen)
2. [Ihn verwenden](#ihn-verwenden)
3. [Per URL](#per-url)
4. [Für das Einbetten durch Redakteure](#für-das-einbetten-durch-redakteure)
5. [Beispiele in den mitgelieferten Designs](#beispiele-in-den-mitgelieferten-designs)

## Einen Ansichtsmodus anlegen

1. Ein Standard-Template für jede Klasse: `design/mytheme/templates/node/view/card.tpl`.

   ```tpl
   <article class="card">
       <h3 class="card-title"><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h3>
   </article>
   ```

2. Overrides für Klassen, die mehr brauchen:

   ```ini
   [mytheme_card_article]
   Source=node/view/card.tpl
   MatchFile=card/article.tpl
   Subdir=templates
   Match[class_identifier]=article
   ```

3. Leeren Sie den Cache der Template-Overrides.

Der Modus braucht kein besonderes Präfix, aber vermeiden Sie Namen, die der Kernel schon verwendet (`full`, `line`, `embed`,
`embed-inline`, `text`, `text_linked`, `plain`, `tiny`, `listitem`, `thumb`, `sitemap`, `print`, `pdf`).

## Ihn verwenden

```tpl
<div class="card-grid">
{foreach $children as $child}{node_view_gui content_node=$child view='card'}{/foreach}
</div>
```

Templates von Layout-Blöcken können ihre Einträge ebenfalls in Ihrem Modus zeichnen ([Block-Ansichtstemplates](../../../../eng-us/help/designers/theme-design/block-view-templates.md)).

## Per URL

`/content/view/card/<node id>` stellt den Modus als Seite dar. Nur Modi in `site.ini [ContentSettings] CachedViewModes`
werden beim Aufruf auf diese Weise im View-Cache gespeichert.

## Für das Einbetten durch Redakteure

Damit Redakteure Ihren Modus beim Einbetten von Inhalten in Rich Text wählen können, fügen Sie ihn in den Einstellungen Ihrer
Erweiterung zu `content.ini [embed] AvailableViewModes[]` hinzu und geben ihm auch ein `content/view/<mode>.tpl`
(Einbettungen zeichnen Objekte mit `content_view_gui`).

## Beispiele in den mitgelieferten Designs

Das Design media verwendet Modi ohne Rahmen für eigene Zwecke: `payload` (das Pagelayout gibt nur den Inhalt aus) und
`layout_preview` (ein reduzierter Rahmen), beide in seinem Pagelayout aus `$module_result.content_info.viewmode` gelesen.

## Verwandte Seiten

- [Ansichtsmodi](../../../../eng-us/help/designers/templates/view-modes.md)
- [Knoten-Templates nach Klasse](../../../../eng-us/help/designers/templates/node-templates-by-class.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
