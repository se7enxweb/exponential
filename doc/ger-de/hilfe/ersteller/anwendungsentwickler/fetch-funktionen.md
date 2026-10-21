---
title: "Fetch-Funktionen: Daten in Templates lesen"
audience: app-creator, developer
category: app-creation
order: 150
summary: Daten in Templates mit fetch() lesen - die Fetch-Funktionen für Inhalte (Knoten, Objekt, Liste, Baum, list_count, verwandte Objekte), ihre Parameter (Filter, Sortierung, Limits, Attributfilter), die Fetch-Funktionen anderer Module und Regeln für die Leistung.
keywords: Fetch-Funktionen, Inhaltsliste, Inhaltsbaum, Attributfilter, Sortierung, Limit, Tiefe, Templates, Anwendungsentwickler, fetch, fetch functions, content list, content tree, list_count, class_filter, attribute_filter, sort_by, limit, offset, depth, function_definition.php, templates, app creator
type: help
---
# Fetch-Funktionen: Daten in Templates lesen

Diese Seite ist für Anwendungsentwickler und Template-Autoren, die Daten in Templates lesen. `fetch( '<module>', '<function>',
hash( ... ) )` ruft eine Funktion auf, die ein Modul in seiner `function_definition.php` deklariert; die des Moduls
content verwenden Sie am häufigsten (`kernel/content/function_definition.php`).

## Inhalt

1. [Die meistgenutzten Fetch-Funktionen für Inhalte](#die-meistgenutzten-fetch-funktionen-für-inhalte)
2. [Parameter von list und tree](#parameter-von-list-und-tree)
3. [Beispiele](#beispiele)
4. [Fetch-Funktionen anderer Module](#fetch-funktionen-anderer-module)
5. [Regeln für die Leistung](#regeln-für-die-leistung)

## Die meistgenutzten Fetch-Funktionen für Inhalte

| Fetch | Gibt zurück |
|---|---|
| `fetch( 'content', 'node', hash( 'node_id', 2 ) )` | einen Knoten |
| `fetch( 'content', 'object', hash( 'object_id', 57 ) )` | ein Objekt |
| `fetch( 'content', 'list', hash( 'parent_node_id', 2, ... ) )` | die Kinder eines Knotens |
| `fetch( 'content', 'tree', hash( 'parent_node_id', 2, ... ) )` | den ganzen Teilbaum |
| `fetch( 'content', 'list_count', ... )`, `'tree_count'` | die Anzahl, für das Blättern |
| `fetch( 'content', 'related_objects', ... )`, `'reverse_related_objects'` | Relationen ([Relationen](../website-ersteller/relationen-und-wiederverwendung.md)) |
| `fetch( 'content', 'class', hash( 'class_id', 'article' ) )` | eine Klasse |
| `fetch( 'content', 'view_top_list', ... )` | die meistgesehenen Knoten |

Alle Fetch-Funktionen für Inhalte geben nur zurück, was der aktuelle Benutzer lesen darf.

## Parameter von list und tree

| Parameter | Beispiel |
|---|---|
| `class_filter_type`, `class_filter_array` | `'include'`, `array( 'article', 'blog_post' )` |
| `sort_by` | `array( 'published', false() )`, `$node.sort_array` |
| `limit`, `offset` | `10`, `$view_parameters.offset` |
| `depth` (tree) | `3` |
| `attribute_filter` | `array( array( 'article/featured', '=', 1 ) )` |
| `extended_attribute_filter` | Filter von Erweiterungen, etwa eztags |
| `main_node_only` | `true()` |
| `ignore_visibility` | `true()`, um verborgene Knoten einzuschließen (für die Administration) |

## Beispiele

Blättern in einem Ordner:

```text
{def $page_limit = 10
     $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                  'offset', $view_parameters.offset,
                                                  'limit', $page_limit,
                                                  'sort_by', $node.sort_array ) )
     $count = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )}
{foreach $children as $child}{node_view_gui content_node=$child view='line'}{/foreach}
{include uri='design:navigator/google.tpl' page_uri=$node.url_alias item_count=$count
         view_parameters=$view_parameters item_limit=$page_limit}
{undef $page_limit $children $count}
```

Hervorgehobene Artikel irgendwo unterhalb einer Sektion:

```text
{def $featured = fetch( 'content', 'tree', hash( 'parent_node_id', 2,
    'class_filter_type', 'include', 'class_filter_array', array( 'article' ),
    'attribute_filter', array( array( 'article/featured', '=', 1 ) ),
    'sort_by', array( 'published', false() ), 'limit', 3 ) )}
```

## Fetch-Funktionen anderer Module

Jedes Modul mit einer `function_definition.php` bietet Fetch-Funktionen an: `user` (`current_user`, `has_access_to`),
`section`, `shop` (`basket`), `layout`, `url`, `search`, `exphelp` und weitere; siehe die Dateien
`kernel/*/function_definition.php`. Erweiterungen fügen eigene hinzu ([Eine eigene Fetch-Funktion schreiben](eine-eigene-fetch-funktion-schreiben.md)).

## Regeln für die Leistung

- Setzen Sie immer ein `limit`; ein Baum ohne Limit kann Tausende Knoten laden.
- Zählen Sie mit `list_count`, nicht durch Abzählen einer geladenen Liste.
- Grenzen Sie Bäume mit `class_filter_array` und `depth` ein.
- Legen Sie aufwendige Fetch-Aufrufe in Cache-Blöcke ([View-Cache und Cache-Blöcke](../website-ersteller/view-cache-und-cache-bloecke.md)).
- Rufen Sie in PHP die Klassenmethoden direkt auf (`eZContentObjectTreeNode::subTreeByNodeID()`), nicht fetch.

## Verwandte Seiten

- [Eine eigene Fetch-Funktion schreiben](eine-eigene-fetch-funktion-schreiben.md)
- [Muster Fetch-Funktion](../../../../eng-us/standards/patterns/fetch-function.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
