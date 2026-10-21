---
title: Knoten und Listen abrufen
audience: designer, developer
category: templates
order: 410
summary: Die Inhalts-Fetches für Seiten - Knoten nach ID, Pfad oder Remote-ID; list und tree mit ihren Parametern (parent_node_id, sort_by, offset, limit, depth, Klassenfilter, Attributfilter, main_node_only, ignore_visibility, load_data_map); Zählungen zum Blättern; durchgerechnete Beispiele.
keywords: fetch content node, fetch content list, fetch content tree, list_count, tree_count, parent_node_id, offset, limit, depth, class_filter_type, class_filter_array, main_node_only, ignore_visibility, load_data_map, remote_id, Knoten abrufen, Listen abrufen
type: help
---
# Knoten und Listen abrufen

Diese Seite ist für Designer, die Seiten und Listen von Seiten abrufen. Alle Parameter unten stammen aus
`kernel/content/function_definition.php`.

## Inhalt

1. [Ein Knoten](#ein-knoten)
2. [Kinder: list](#kinder-list)
3. [Nachkommen: tree](#nachkommen-tree)
4. [Zählungen zum Blättern](#zählungen-zum-blättern)
5. [Beispiele](#beispiele)

## Ein Knoten

```tpl
{def $home = fetch( 'content', 'node', hash( 'node_id', 2 ) )}
{def $about = fetch( 'content', 'node', hash( 'node_path', 'about-us' ) )}
{def $info = fetch( 'content', 'node', hash( 'remote_id', 'mytheme-site-info' ) )}
```

Parameter: `node_id`, `node_path`, `remote_id`, `language_code`. Das Ergebnis ist `false`, wenn der Knoten nicht existiert
oder nicht gelesen werden darf.

## Kinder: list

```tpl
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                 'sort_by', $node.sort_array,
                                                 'class_filter_type', 'include',
                                                 'class_filter_array', array( 'article', 'blog_post' ),
                                                 'offset', $offset,
                                                 'limit', 10 ) )}
```

| Parameter | Bedeutung |
|---|---|
| `parent_node_id` | wessen Kinder (Pflicht) |
| `sort_by` | Sortierfelder ([Fetch-Filter und Sortierung](fetch-filter-und-sortierung.md)); `$node.sort_array` verwendet die Reihenfolge, die der Redakteur gesetzt hat |
| `offset`, `limit` | Blättern |
| `class_filter_type`, `class_filter_array` | diese Klassenbezeichner einschließen (`include`) oder ausschließen (`exclude`) |
| `attribute_filter`, `extended_attribute_filter` | nach Feldwerten oder eigenen Joins filtern |
| `depth`, `depth_operator` | bei `tree`: wie tief |
| `main_node_only` | nur der Hauptort jedes Objekts |
| `ignore_visibility` | versteckte Knoten einschließen (in öffentlichen Designs vermeiden) |
| `only_translated`, `language` | Behandlung von Sprachen |
| `objectname_filter` | Namen, die mit einem Buchstaben beginnen (Listen von A bis Z) |
| `group_by`, `limitation`, `as_object`, `load_data_map` | fortgeschritten: Gruppierung, Übersteuern von Berechtigungen, einfache Arrays, ob Felddaten sofort geladen werden |

## Nachkommen: tree

Dieselben Parameter, für jede Ebene unter dem Elternknoten; mit `depth` zur Begrenzung:

```tpl
{def $all_articles = fetch( 'content', 'tree', hash( 'parent_node_id', 2,
                                                     'class_filter_type', 'include',
                                                     'class_filter_array', array( 'article' ),
                                                     'sort_by', array( 'published', false() ),
                                                     'limit', 5 ) )}
```

Ein tree-Fetch über eine große Website ist teuer: Filtern Sie immer nach Klasse und begrenzen Sie.

## Zählungen zum Blättern

`list_count` und `tree_count` nehmen dieselben Filterparameter (ohne `sort_by`, `offset`, `limit`):

```tpl
{def $total = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id,
                                                    'class_filter_type', 'include',
                                                    'class_filter_array', array( 'article' ) ) )}
```

## Beispiele

Die neuesten drei Nachrichten auf der Startseite:

```tpl
{def $news_folder = fetch( 'content', 'node', hash( 'remote_id', 'mytheme-news-folder' ) )}
{if $news_folder}
    {def $latest = fetch( 'content', 'list', hash( 'parent_node_id', $news_folder.node_id,
                                                   'sort_by', array( 'published', false() ),
                                                   'limit', 3 ) )}
    {foreach $latest as $item}{node_view_gui content_node=$item view='line'}{/foreach}
    {undef $latest}
{/if}
{undef $news_folder}
```

## Verwandte Seiten

- [Fetch-Grundlagen](fetch-grundlagen.md)
- [Fetch-Filter und Sortierung](fetch-filter-und-sortierung.md)
- [Seitenumbruch](seitenumbruch.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
