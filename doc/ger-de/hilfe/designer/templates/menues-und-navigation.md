---
title: Menüs und Navigation
audience: designer, developer
category: templates
order: 670
summary: Das Hauptmenü, Untermenüs und Links in der Fußzeile einer Website in Templates bauen - die Kinder der Wurzel mit Limits und Klassenfiltern abrufen, die aktuelle Seite mit aria-current markieren, eine zweite Ebene, das Menü cachen und barrierefreie Menüschaltflächen für kleine Bildschirme.
keywords: Menü, Navigation, Hauptmenü, oberes Menü, Untermenü, aktuelle Seite, aria-current, nav, Kinder für ein Menü abrufen, Menü-Cache, mobiles Menü, Menüschalter, menu, navigation, main menu, top menu, sub menu, current page, fetch children menu, menu cache, mobile menu, menu toggle
type: help
---
# Menüs und Navigation

Diese Seite ist für Designer, die die Menüs einer Website bauen. Menüs sind Inhalte: Redakteure fügen auf der obersten Ebene
einen Ordner hinzu, und er erscheint im Menü. Das Template ruft die Menüeinträge ab und markiert die aktuelle Seite.

## Inhalt

1. [Das Hauptmenü](#das-hauptmenü)
2. [Die aktuelle Seite markieren](#die-aktuelle-seite-markieren)
3. [Eine zweite Ebene](#eine-zweite-ebene)
4. [Das Menü cachen](#das-menü-cachen)
5. [Kleine Bildschirme](#kleine-bildschirme)

## Das Hauptmenü

```tpl
{* pagelayout/menu.tpl *}
{def $root_node_id = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $menu_items = fetch( 'content', 'list', hash( 'parent_node_id', $root_node_id,
                                                   'sort_by', array( 'priority', true() ),
                                                   'class_filter_type', 'include',
                                                   'class_filter_array', array( 'folder', 'frontpage', 'landing_page' ),
                                                   'limit', 8 ) )}
<nav class="main-nav" aria-label="{'Main navigation'|i18n( 'design/mytheme' )}">
    <ul>
    {foreach $menu_items as $item}
        <li><a href={$item.url_alias|ezurl}>{$item.name|wash}</a></li>
    {/foreach}
    </ul>
</nav>
{undef $root_node_id $menu_items}
```

`content.ini [NodeSettings] RootNode` ist die Wurzel des Baums des Siteaccess (das Pagelayout von starter liest sie auf
dieselbe Weise). Verwenden Sie die Klassenbezeichner Ihrer Installation.

## Die aktuelle Seite markieren

Vergleichen Sie mit dem Pfad der aktuellen Seite, den das Pagelayout in `$module_result.path` hat:

```tpl
{def $current_path_ids = array()}
{if is_array( $module_result.path )}
    {foreach $module_result.path as $step}{if is_set( $step.node_id )}{set $current_path_ids = $current_path_ids|append( $step.node_id )}{/if}{/foreach}
{/if}
...
<li{if $current_path_ids|contains( $item.node_id )} class="is-active"{/if}>
    <a href={$item.url_alias|ezurl}{if eq( $item.node_id, first_set( $module_result.node_id, 0 ) )} aria-current="page"{/if}>{$item.name|wash}</a>
</li>
```

`aria-current="page"` sagt Screenreadern, welcher Eintrag die aktuelle Seite ist; die Klasse gestaltet den aktiven Zweig.

## Eine zweite Ebene

Rufen Sie nur die Kinder des aktiven oberen Eintrags ab, nicht die jedes Eintrags:

```tpl
{foreach $menu_items as $item}
    {if $current_path_ids|contains( $item.node_id )}
        {def $sub_items = fetch( 'content', 'list', hash( 'parent_node_id', $item.node_id, 'sort_by', $item.sort_array, 'limit', 12 ) )}
        ...
        {undef $sub_items}
    {/if}
{/foreach}
```

## Das Menü cachen

Das Menü ist für alle mit denselben Rollen auf derselben Seite gleich:

```tpl
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) ) subtree_expiry=$root_node_id}
    {include uri='design:pagelayout/menu.tpl'}
{/cache-block}
```

([Cache-Block](cache-block.md)). Menüs der ezwebin-Familie werden stattdessen mit `menu.ini [SelectedMenu]` eingerichtet
([Das Design ezwebin als Grundlage](../theme-design/ezwebin-als-grundlage.md)).

## Kleine Bildschirme

Eine Menüschaltfläche mit `aria-expanded` und `aria-controls`, von einer Skriptdatei umgeschaltet, nie durch Inline-Code
([Kein Inline-JavaScript](../../../../eng-us/help/designers/theme-design/no-inline-javascript.md)); das Menü bleibt ohne das Skript benutzbar (zum Beispiel
eine Liste von Links, die das Skript einklappt).

## Verwandte Seiten

- [Brotkrumennavigation](brotkrumen.md)
- [Knoten und Listen abrufen](knoten-und-listen-abrufen.md)
- [Tastatur und Fokus](../theme-design/tastatur-und-fokus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
