---
title: Eigene Template-Blöcke
audience: designer, developer, website-creator, editor
category: build-two-ways
order: 540
summary: Der Template-Block ist die Brücke zwischen den beiden Wegen; ein Entwickler schreibt explayouts/tpl_block/<name>.tpl, ein Redakteur platziert es mit dem Layout-Editor überall, indem er seinen Namen eintippt. Getestetes Beispiel latest_fitness und wie das Theme seine eigenen Template-Blöcke zuordnet.
keywords: Template-Block, tpl_block, Template name, block_name, explayouts/tpl_block, tpl_block_template, Platzhalter, mysite, fetch, Brücke, Kopfzeile, Fußzeile, Brotkrumenpfad, template block, placeholders, bridge, header, footer, breadcrumb
type: help
---
# Eigene Template-Blöcke

Ein **Template block** (Blocktyp `tpl_block`, Gruppe *Placeholders* der Palette) zeigt ein Template, dessen Namen Sie
im Editor eintippen. Der Entwickler entscheidet *was* (das Template), der Redakteur entscheidet *wo* (Layout, Zone und
Reihenfolge). Kopfzeile, Brotkrumenpfad und Fußzeile von Fit & Healthy sind Template-Blöcke ([Wie die Kopfzeile aufgebaut ist](../../../eng-us/help/build-two-ways/how-the-header-is-built.md)).

## Inhalt

1. [Das Template schreiben](#1-das-template-schreiben)
2. [Es in einem Layout platzieren](#2-es-in-einem-layout-platzieren)
3. [Das Ergebnis](#3-das-ergebnis)
4. [Wie ein Template-Block sein Template findet](#wie-ein-template-block-sein-template-findet)

## 1. Das Template schreiben

In Ihrer Design-Erweiterung: `extension/mysite/design/media/templates/explayouts/tpl_block/latest_fitness.tpl`, hier die
drei neuesten Artikel von Fitness (Knoten 92):

```smarty
{def $latest = fetch( 'content', 'list', hash( 'parent_node_id', 92,
                                                'class_filter_type', 'include',
                                                'class_filter_array', array( 'ng_article' ),
                                                'sort_by', array( 'published', false() ),
                                                'limit', 3 ) )}
<div class="container latest-fitness">
    <h2>{'Latest in Fitness'|i18n( 'mysite' )}</h2>
    <ul>
    {foreach $latest as $item}
        <li><a href={$item.url_alias|ezurl}>{$item.name|wash}</a></li>
    {/foreach}
    </ul>
</div>
{undef $latest}
```

Der Name darf Kleinbuchstaben, Ziffern und `_` enthalten. Leeren Sie nach dem Hinzufügen der Datei den Template-Cache
(`./console exp:cache clear --tag=template`).

## 2. Es in einem Layout platzieren

Öffnen Sie das Layout im Editor, öffnen Sie die Blockpalette, scrollen Sie zu **Placeholders** und ziehen Sie
**Template block** in eine Zone. Wählen Sie ihn aus, öffnen Sie den Reiter **Design** und tippen Sie den Namen in
**Template name** ein: `latest_fitness`. Der Block lautet dann „Template block: **latest_fitness**“. **Layout veröffentlichen**.

![Ein Template-Block im Layout Prefooter mit dem Template name latest_fitness im Reiter Design](../../../eng-us/help/build-two-ways/images/tb-02-template-block-latest-fitness.webp)

## 3. Das Ergebnis

![Die Liste Latest in Fitness auf einer Artikelseite](../../../eng-us/help/build-two-ways/images/tb-03-latest-fitness-on-site.webp)

## Wie ein Template-Block sein Template findet

Im Media-Design fragt `explayouts/block/tpl_block.tpl` den Operator `tpl_block_template()` (in
`extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php`):

1. eine feste Zuordnung der eigenen Block-IDs des Themes (13 Kopfzeile, 477 Brotkrumenpfad, 14 Fußzeile sowie die Teile von Artikeln und Themen);
2. nach Platz: ein Wurzelblock in der Zone `header`, ein Block in einer Spalte der Zone `header`, ein Wurzelblock in der Zone `footer`;
3. nach seinem **Template name**: `explayouts/tpl_block/<name>.tpl`, wenn ein Design diese Datei hat. Ein Name ohne Datei
   stellt nichts dar (und keinen leeren Wrapper).

Ein Template-Block, den Sie in einer Zone `header` oder `footer` des gemeinsamen Layouts hinzufügen, wird also nach Regel 2
als Kopfzeile, Brotkrumenpfad oder Fußzeile des Themes genommen, egal welchen Namen Sie eintippen. Setzen Sie Ihre eigenen
Template-Blöcke in andere Zonen (`post_header`, `main`, `pre_footer`) oder in ein anderes Layout.

Das Standard-Design von Layouts hat eigene Platzhalter unter
`extension/explayouts/design/standard/templates/explayouts/tpl_block/` (`header.tpl`, `breadcrumb.tpl`, `footer.tpl`);
ein Design ohne den Operator des Media-Themes bindet `design:explayouts/tpl_block/<name>.tpl` direkt ein.

## Verwandte Seiten

- [Wie die Kopfzeile aufgebaut ist](../../../eng-us/help/build-two-ways/how-the-header-is-built.md)
- [Einen Blocktyp hinzufügen](blocktyp-hinzufuegen.md)
- [Ihre eigene Design-Erweiterung](eigene-design-erweiterung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
