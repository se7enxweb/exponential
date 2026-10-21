---
title: Inhalte in Templates abrufen
audience: developer, designer, app-creator
category: extension-development
order: 130
summary: fetch() liest Inhalte in Templates - einen Knoten, Unterknoten, einen Unterbaum, Anzahlen, Relationen, mit Klassen- und Attributfiltern, Tiefe und Sortierung. Parameter aus dem Kernel, Beispiele von der Engine dargestellt.
keywords: fetch, Inhaltsliste, Baum, Anzahl, Filter, Sortierung, Seitenaufteilung, Leistung, content list, tree, list_count, tree_count, node, object, class_filter_type, class_filter_array, attribute_filter, sort_by, depth, limit, offset, pagination, performance
type: help
---
# Inhalte in Templates abrufen

Teil 2, Kapitel 13. Templates holen Inhalte mit `fetch( <module>, <function>, hash( <parameters> ) )`. Jedes Modul
deklariert seine Fetch-Funktionen und deren Parameter in einer `function_definition.php`; die des Moduls `content`
stehen in `kernel/content/function_definition.php`, und jeder Parameter auf dieser Seite ist von dort übernommen. Die
Beispiele wurden von der Template-Engine auf einer Installation mit Beispielinhalten dargestellt.

## Die alltäglichen Funktionen

| Aufruf | Liefert |
|---|---|
| `fetch( 'content', 'node', hash( 'node_id', 2 ) )` | einen Knoten |
| `fetch( 'content', 'object', hash( 'object_id', 1 ) )` | ein Objekt |
| `fetch( 'content', 'list', hash( 'parent_node_id', 2 ) )` | die Unterknoten eines Knotens |
| `fetch( 'content', 'list_count', hash( 'parent_node_id', 2 ) )` | wie viele Unterknoten |
| `fetch( 'content', 'tree', hash( 'parent_node_id', 2 ) )` | den ganzen Unterbaum unter einem Knoten |
| `fetch( 'content', 'tree_count', hash( 'parent_node_id', 2 ) )` | wie viele Knoten der Unterbaum hat |
| `fetch( 'content', 'related_objects', hash( 'object_id', ... ) )` | verknüpfte Objekte |
| `fetch( 'content', 'reverse_related_objects', hash( 'object_id', ... ) )` | Objekte, die auf dieses verweisen |

Die vollständige Liste der Fetch-Funktionen aller Module steht in [Eingebaute Fetch-Funktionen nach Modul](../teil-8-referenz/referenz-der-fetch-funktionen.md).

## Parameter von list und tree

| Parameter | Bedeutung | Beispiel |
|---|---|---|
| `parent_node_id` | wo begonnen wird | `$node.node_id` |
| `limit`, `offset` | Seitenaufteilung | `'limit', 10, 'offset', $view_parameters.offset` |
| `sort_by` | Sortierung: ein Paar oder eine Liste von Paaren; `true()` aufsteigend | `array( 'published', false() )` |
| `class_filter_type`, `class_filter_array` | nur (`include`) oder nicht (`exclude`) diese Klassen | `'include'`, `array( 'article' )` |
| `attribute_filter` | Bedingungen für Attribute | siehe unten |
| `depth`, `depth_operator` | wie tief in einem Baum | `3`, `'le'` |
| `main_node_only` | nur Hauptplatzierungen | `true()` |
| `ignore_visibility` | versteckte Knoten einschließen | `true()` (nur für Admin-Ansichten) |
| `language`, `only_translated` | Übersetzungen | `'ger-DE'` |
| `load_data_map` | Attribute mit den Knoten laden | `false()` für große Listen, deren Namen Sie nur ausgeben |
| `as_object` | Objekte oder Arrays | `false()` für rohe Zeilen |

## Beispiele

**Die fünf neuesten Artikel irgendwo unter dem obersten Knoten und wie viele es sind:**

```text
{def $news = fetch( 'content', 'tree', hash( 'parent_node_id', 2,
                                             'class_filter_type', 'include',
                                             'class_filter_array', array( 'article' ),
                                             'sort_by', array( 'published', false() ),
                                             'limit', 5 ) )
     $count = fetch( 'content', 'tree_count', hash( 'parent_node_id', 2,
                                                    'class_filter_type', 'include',
                                                    'class_filter_array', array( 'article' ) ) )}
<p>{$count} articles, the newest five:</p>
<ul>
{foreach $news as $item}
    <li><a href={$item.url_alias|ezurl}>{$item.name|wash}</a></li>
{/foreach}
</ul>
```

**Inhalte des letzten Jahres, drei Ebenen tief, nach Priorität und dann nach Name sortiert:**

```text
{def $recent = fetch( 'content', 'tree', hash( 'parent_node_id', 2,
                     'attribute_filter', array( array( 'published', '>', sub( currentdate(), 31536000 ) ) ),
                     'depth', 3,
                     'limit', 3,
                     'sort_by', array( array( 'priority', true() ), array( 'name', true() ) ) ) )}
```

`attribute_filter` nimmt eine Liste von Bedingungen `array( <attribute>, <operator>, <value> )`. Das Attribut ist eine
Eigenschaft des Knotens (`published`, `modified`, `name`, `priority`, `section`) oder `<class>/<attribute>` für ein Feld
einer Klasse (`article/rating`). Die Operatoren sind `=`, `!=`, `<`, `>`, `<=`, `>=`, `in`, `not_in`, `between`,
`not_between`, `like` (`createAttributeFilterSQLStrings()` in `kernel/classes/ezcontentobjecttreenode.php`). Ein erstes
Element `'or'` macht daraus eine Oder-Verknüpfung: `array( 'or', array( ... ), array( ... ) )`.

**Die Unterknoten eines Ordners seitenweise:**

```text
{def $page_limit = 10
     $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                 'offset', $view_parameters.offset,
                                                 'limit', $page_limit,
                                                 'sort_by', $node.sort_array ) )
     $children_count = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )}
{include uri='design:navigator/google.tpl'
         page_uri=$node.url_alias
         item_count=$children_count
         view_parameters=$view_parameters
         item_limit=$page_limit}
```

`$node.sort_array` verwendet die Sortierung, die der Redakteur in der Administration für den Ordner gewählt hat; das ist
fast immer das, was der Besucher sehen sollte.

## Zugriffsrechte werden angewendet

Fetch liefert nur, was der aktuelle Benutzer lesen darf: unveröffentlichte, versteckte (außer mit `ignore_visibility`)
und verbotene Inhalte fehlen. Eine Liste, die für anonyme Besucher kurz aussieht, ist meist richtig; prüfen Sie die
Rollen, bevor Sie den Fetch verdächtigen.

## Leistung

| Tun Sie | Warum |
|---|---|
| Einmal abrufen, vor der Schleife | ein Fetch in einem `foreach` läuft einmal je Element |
| `limit` verwenden | ein Baum-Fetch ohne Limit kann Tausende von Knoten laden |
| Für Zahlen die `_count`-Funktionen verwenden | sie laden die Knoten nicht |
| `class_filter_array` und `depth` verwenden | die Datenbank übernimmt das Filtern |
| `load_data_map` auf `false()` setzen, wenn Sie nur Namen ausgeben | Attribute werden nicht geladen |
| Ergebnisse in einem `cache-block` halten, wenn sie sich selten ändern | siehe [Caching und Templates](caching-und-templates.md) |

Schalten Sie während der Entwicklung `[DatabaseSettings] SQLOutput` ein, um die Abfragen einer Seite zu sehen.

## Verwandte Seiten

- Zurück: [View-Modi für Builder](view-modi-fuer-builder.md)
- Weiter: [Die Template-Operatoren, die Builder am häufigsten nutzen](die-wichtigsten-template-operatoren-fuer-builder.md)
- [Eingebaute Fetch-Funktionen nach Modul](../teil-8-referenz/referenz-der-fetch-funktionen.md)
- [Fetch-Funktionen](../teil-3-erweiterungsentwickler/fetch-funktionen.md): eigene schreiben
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
