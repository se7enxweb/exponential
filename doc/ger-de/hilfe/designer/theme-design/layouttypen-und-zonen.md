---
title: Layouttypen und Zonen
audience: designer
category: theme-design
order: 710
summary: Die in explayouts.ini definierten Layouttypen (1 bis 4 Spalten, Hero, Seitenleisten, Featured, Mosaik, Kopf/Haupt/Fuß, Admin-Typen), ihre Zonen, das Template, das jedes Raster zeichnet, und wie Sie einen eigenen Layouttyp hinzufügen.
keywords: Layouttypen, LayoutType, Zonen, Zones[], Layout-Template, 1_column, 2_column, hero, sidebar_left, layout_2, Kopf Haupt Fuß, Raster, Layouttyp hinzufügen, explayouts.ini, layout types, zones, layout template, header main footer, grid, add layout type
type: help
---
# Layouttypen und Zonen

Diese Seite ist für Designer, die entscheiden, welche Raster Redakteure wählen können. Ein **Layouttyp** legt die Zonen eines Layouts
fest und optional das Template, das sie platziert. Redakteure wählen den Typ, wenn sie ein Layout anlegen, und können ihn später nicht
ändern, weil Blöcke an Zonen hängen.

## Inhalt

1. [Die mitgelieferten Layouttypen](#die-mitgelieferten-layouttypen)
2. [Ein Layouttyp in den Einstellungen](#ein-layouttyp-in-den-einstellungen)
3. [Das Template eines Layouttyps](#das-template-eines-layouttyps)
4. [Einen Layouttyp hinzufügen](#einen-layouttyp-hinzufügen)

## Die mitgelieferten Layouttypen

Aus `extension/explayouts/settings/explayouts.ini.append.php`:

| Typ | Name | Zonen | Template |
|---|---|---|---|
| `1_column` | 1 column | `main` | `layouts/1_column.tpl` |
| `2_column` | 2 columns | `left`, `right` | `layouts/2_column.tpl` |
| `3_column` | 3 columns | `left`, `main`, `right` | `layouts/3_column.tpl` |
| `4_column` | 4 columns | `col1` bis `col4` | `layouts/4_column.tpl` |
| `hero` | Hero + 3 columns | `top`, `left`, `main`, `right` | `layouts/hero.tpl` |
| `sidebar_left`, `sidebar_right` | Sidebar left / right | `sidebar`, `main` | `layouts/sidebar_*.tpl` |
| `featured` | Featured | `hero`, `feature1` bis `feature3`, `bottom` | `layouts/featured.tpl` |
| `mosaic` | Mosaic | `a` bis `e` | `layouts/mosaic.tpl` |
| `layout_1` | Single zone | `main` | keines |
| `layout_2` | Header / Main / Footer | `header`, `post_header`, `main`, `pre_footer`, `footer` | keines |
| `layout_4` | Header / Left / Right / Footer | `header`, `post_header`, `left`, `right`, `pre_footer`, `footer` | keines |
| `admin_3col`, `admin_2col`, `admin_full` | Admin-Typen (`Group=admin`) | `header`, `topmenu`, `left` (3col, 2col), `right` (3col), `main_top`, `main`, `main_bottom`, `footer` | keines; vom Pagelayout von admin4l gezeichnet |

Bei einem Typ ohne `Template=` zeichnet `layout.tpl` seine Zonen untereinander; Ihr CSS platziert sie.

## Ein Layouttyp in den Einstellungen

```ini
[LayoutType_2_column]
Name=2 columns
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/2_column.tpl
Zones[]=left
Zones[]=right
```

## Das Template eines Layouttyps

`layouts/2_column.tpl` zeichnet jede Zone an ihrer Stelle und lässt `zone_blocks.tpl` die Blöcke zeichnen:

```tpl
<div class="slb slb-2-column" data-layout-id="{$layout.id|wash}">
    {foreach $layout.zones as $zone}
        {if eq( $zone.identifier, 'left' )}
            <div class="sevenx-zone sevenx-zone-left" data-zone-id="{$zone.id|wash}">
                {include uri='design:explayouts/zone_blocks.tpl' zone=$zone module_result=$module_result}
            </div>
        {/if}
        ...
    {/foreach}
</div>
```

## Einen Layouttyp hinzufügen

In `settings/explayouts.ini.append.php` Ihrer Design-Erweiterung:

```ini
[LayoutType_mytheme_split]
Name=Split: wide and narrow
Template=explayouts/layouts/mytheme_split.tpl
Zones[]=wide
Zones[]=narrow
```

und das Template `design/mytheme/templates/explayouts/layouts/mytheme_split.tpl` im Stil oben. Verwenden Sie im Bezeichner ein Präfix
Ihres Themes. Leeren Sie den INI-Cache und den Template-Override-Cache; der Typ erscheint, wenn Redakteure ein Layout anlegen. Das Theme
starter beschränkt mit seiner eigenen Einstellung `cjw_jumper.ini [LayoutSettings] LayoutTypes[]`, welche Layouttypen zu seinem Design
gehören.

## Verwandte Seiten

- [Layouts für Designer](layouts-fuer-designer.md)
- [Blockdefinitionen](blockdefinitionen.md)
- [Gemeinsame Layouts und verknüpfte Zonen](gemeinsame-layouts-und-verknuepfte-zonen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
