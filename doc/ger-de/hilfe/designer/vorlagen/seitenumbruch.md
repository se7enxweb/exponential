---
title: Seitenumbruch
audience: designer, developer
category: templates
order: 690
summary: Lange Listen auf Seiten verteilen - der View-Parameter offset, ein Fetch mit offset und limit plus einer Zählung, die mitgelieferten Navigator-Templates (navigator/google.tpl und andere) und ihre Parameter, ein eigener barrierefreier Seitenwähler und wie das Blättern mit dem View-Cache zusammenwirkt.
keywords: Seitenumbruch, Blättern, Seitenwähler, offset, view_parameters, navigator, google.tpl, item_count, item_limit, page_uri, page_uri_suffix, Listen blättern, barrierefreies Blättern, pagination, paging, pager, list paging, accessible pagination
type: help
---
# Seitenumbruch

Diese Seite ist für Designer, die lange Listen auf Seiten verteilen: Ordnerinhalte, Suchergebnisse, Archive.

## Inhalt

1. [Der View-Parameter offset](#der-view-parameter-offset)
2. [Eine Seite abrufen](#eine-seite-abrufen)
3. [Der mitgelieferte Navigator](#der-mitgelieferte-navigator)
4. [Ein eigener Seitenwähler](#ein-eigener-seitenwähler)
5. [Caching](#caching)

## Der View-Parameter offset

Seite 2 eines Ordners mit 10 Einträgen pro Seite ist `/news/(offset)/10`. Die Teile `(name)/value` einer URL kommen in
View-Templates als `$view_parameters` an (hier `$view_parameters.offset`).

## Eine Seite abrufen

```tpl
{def $limit = 10
     $offset = first_set( $view_parameters.offset, 0 )|int
     $items = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'sort_by', $node.sort_array,
                                              'offset', $offset, 'limit', $limit ) )
     $total = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )}
```

Verwenden Sie in `list` und `list_count` dieselben Filter, sonst passen die Seitenzahlen nicht zu den Einträgen.

## Der mitgelieferte Navigator

`design/standard/templates/navigator/` hat `google.tpl` (Seitenzahlen mit vorheriger und nächster Seite), `simple.tpl`,
`alphabetical.tpl` und `monthview.tpl`. Das Such-Template des Kernels bindet es so ein:

```tpl
{include name=Navigator
         uri='design:navigator/google.tpl'
         page_uri='/content/search'
         page_uri_suffix=concat( '?SearchText=', $search_text|urlencode )
         item_count=$search_count
         view_parameters=$view_parameters
         item_limit=$page_limit}
```

Für einen Ordner: `page_uri=$node.url_alias`, `item_count=$total`, `item_limit=$limit`. `left_max` und `right_max` begrenzen,
wie viele Seitenzahlen um die aktuelle herum erscheinen (Standard 7 und 6). Kopieren Sie `navigator/google.tpl` in Ihr Design,
um sein Markup zu ändern.

## Ein eigener Seitenwähler

Markup, das für alle funktioniert:

```tpl
{def $pages = ceil( div( $total, $limit ) ) $current = sum( div( $offset, $limit )|floor, 1 )}
{if gt( $pages, 1 )}
<nav class="pager" aria-label="{'Pages'|i18n( 'design/mytheme' )}">
    <ul>
    {for 1 to $pages as $p}
        <li>{if eq( $p, $current )}<a aria-current="page">{$p}</a>{else}<a href={concat( $node.url_alias, '/(offset)/', mul( $p|dec, $limit ) )|ezurl}>{$p}</a>{/if}</li>
    {/for}
    </ul>
</nav>
{/if}
```

Geben Sie den Links zur vorherigen und nächsten Seite sichtbaren Text („Zurück“, „Weiter“), nicht nur Pfeile.

## Caching

Der Versatz ist Teil des Schlüssels des View-Cache, sodass jede Seite einer Liste getrennt gecacht und geleert wird, wenn sich
der Inhalt des Ordners ändert.

## Verwandte Seiten

- [Knoten und Listen abrufen](knoten-und-listen-abrufen.md)
- [Vollansicht](vollansicht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
