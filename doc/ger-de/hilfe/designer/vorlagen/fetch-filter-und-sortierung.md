---
title: Fetch-Filter und Sortierung
audience: designer, developer
category: templates
order: 420
summary: Inhaltslisten sortieren (published, modified, name, priority, path, depth, class_identifier, section, attribute ...) und nach Klasse und Feldwerten filtern, mit attribute_filter und seinen Operatoren (=, !=, <, >, <=, >=, in, not_in, between, not_between, like, not_like).
keywords: sort_by, Sortierung, attribute_filter, Filter, class_filter_array, published, priority, name, like, between, in, not_in, Fetch-Filter, Sortierreihenfolge, sorting, filter, fetch filter, sort order
type: help
---
# Fetch-Filter und Sortierung

Diese Seite ist eine Referenz für Designer, die Listen von Knoten in `fetch( 'content', 'list' | 'tree' ... )` sortieren und
filtern.

## Inhalt

1. [Sortieren](#sortieren)
2. [Klassenfilter](#klassenfilter)
3. [Attributfilter](#attributfilter)
4. [Beispiele](#beispiele)

## Sortieren

`sort_by` ist ein Array `array( <field>, <ascending> )` oder ein Array solcher Paare für mehrere Ebenen:

```tpl
'sort_by', array( 'published', false() )                                  {* newest first *}
'sort_by', array( array( 'priority', true() ), array( 'name', true() ) )  {* priority, then name *}
'sort_by', $node.sort_array                                               {* the order the editor chose for this folder *}
```

Felder, die `eZContentObjectTreeNode` behandelt (`kernel/classes/ezcontentobjecttreenode.php`): `path`, `path_string`,
`published`, `modified`, `modified_subnode`, `section`, `node_id`, `contentobject_id`, `depth`, `class_identifier`,
`class_name`, `priority`, `name` und `attribute` (nach dem Wert eines Felds: `array( 'attribute', false(), 'event/from_time' )`).

## Klassenfilter

```tpl
'class_filter_type', 'include', 'class_filter_array', array( 'article', 'blog_post' )
'class_filter_type', 'exclude', 'class_filter_array', array( 'image' )
```

Filtern Sie im Fetch nach Klasse, nicht mit `{if}` in der Schleife: Dann liefert die Datenbank nur, was Sie zeigen.

## Attributfilter

`attribute_filter` ist ein Array von Bedingungen, wahlweise beginnend mit `'and'` oder `'or'`:

```tpl
'attribute_filter', array( 'and',
                           array( 'event/from_time', '>=', currentdate() ),
                           array( 'section', '=', 1 ) )
```

Jede Bedingung ist `array( <field>, <operator>, <value> )`, wobei das Feld `<class>/<attribute>` oder eine Eigenschaft des
Knotens wie `published`, `modified`, `section`, `name` ist. Operatoren: `=`, `!=`, `<`, `>`, `<=`, `>=`, `in`, `not_in`,
`between`, `not_between` (Wert: `array( from, to )`), `like`, `not_like` (mit `*` als Platzhalter).

## Beispiele

Kommende Termine, die nächsten zuerst:

```tpl
{def $events = fetch( 'content', 'tree', hash( 'parent_node_id', $node.node_id,
                                               'class_filter_type', 'include',
                                               'class_filter_array', array( 'event' ),
                                               'attribute_filter', array( array( 'event/from_time', '>=', currentdate() ) ),
                                               'sort_by', array( 'attribute', true(), 'event/from_time' ),
                                               'limit', 10 ) )}
```

Hinweis: `currentdate()` in einem Template im View-Cache ist auf den Zeitpunkt des Cachens eingefroren; eine Vollansicht,
die kommende Termine zeigt, sollte ein kurzes `cache_ttl` setzen ([Caching für Designer](../theme-design/caching-fuer-designer.md)).

## Verwandte Seiten

- [Knoten und Listen abrufen](knoten-und-listen-abrufen.md)
- [Fetch-Leistung](fetch-leistung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
