---
title: Inhalte in Templates abrufen
audience: cms-developer
category: cms-development
order: 25
summary: Für CMS-Entwickler die Fetch-Funktionen, die handgeschriebene Abfragen ersetzen. Die Seite behandelt einzelne Knoten und Objekte, Unterelemente und Teilbäume, Filter nach Klasse und Attribut, Sortierung, Blättern mit dem View-Parameter offset, Zählungen, Relationen und Suche sowie die Regeln, die Fetches schnell und cachefreundlich halten.
keywords: fetch, Fetch-Funktionen, content list, content tree, list_count, tree_count, node, object, class_filter_type, class_filter_array, attribute_filter, sort_by, limit, offset, depth, main_node_only, Blättern, Paginierung, navigator, view_parameters, Leistung, fetch functions, pagination, performance
type: help
---
# Inhalte in Templates abrufen

Diese Seite richtet sich an Entwickler, die in Templates Listen und Nachschlagevorgänge brauchen: die neuesten News,
die Veranstaltungen des nächsten Monats, die Unterelemente eines Ordners, verwandte Elemente. Templates führen kein SQL
aus; sie rufen **Fetch-Funktionen** auf, die die Module festlegen, und jeder Fetch prüft Berechtigungen, Sichtbarkeit
und Sprachen für Sie.

## Inhalt

1. [Der Aufruf](#der-aufruf)
2. [Die Fetches, die Sie am häufigsten verwenden](#die-fetches-die-sie-am-häufigsten-verwenden)
3. [Unterelemente und Teilbäume](#unterelemente-und-teilbäume)
4. [Nach Klasse und Attribut filtern](#nach-klasse-und-attribut-filtern)
5. [Sortieren](#sortieren)
6. [Blättern](#blättern)
7. [Regeln für schnelle Fetches](#regeln-für-schnelle-fetches)
8. [Wenn ein Fetch nicht genügt](#wenn-ein-fetch-nicht-genügt)

## Der Aufruf

```smarty
{def $result = fetch( '<module>', '<function>', hash( '<parameter>', <value>, ... ) )}
```

Die Funktionen und ihre Parameter sind in der `function_definition.php` jedes Moduls deklariert, für Inhalte in
`kernel/content/function_definition.php`. Einzelne Ergebnisse können `false` sein (fehlt oder ist für diesen Benutzer
nicht lesbar); prüfen Sie sie also: `{if $node}...{/if}` ([Fetch-Grundlagen](../designer/vorlagen/fetch-grundlagen.md)).

## Die Fetches, die Sie am häufigsten verwenden

| Aufruf | Gibt zurück |
|---|---|
| `fetch( 'content', 'node', hash( 'node_id', 2 ) )` | einen Knoten (auch über `node_path` oder `remote_id`) |
| `fetch( 'content', 'object', hash( 'object_id', 57 ) )` | ein Objekt |
| `fetch( 'content', 'list', hash( ... ) )`, `list_count` | Unterelemente eines Knotens und ihre Anzahl |
| `fetch( 'content', 'tree', hash( ... ) )`, `tree_count` | alle Nachkommen, mit Tiefenbegrenzung |
| `fetch( 'content', 'related_objects', hash( ... ) )`, `reverse_related_objects` | Relationen ([Relationen und Einbettungen](relationen-und-einbettungen.md)) |
| `fetch( 'content', 'keyword', hash( ... ) )` | Objekte nach Schlüsselwort |
| `fetch( 'content', 'search', hash( 'text', ... ) )` | Suchergebnisse ([Suche](suche.md)) |
| `fetch( 'user', 'current_user' )` | den angemeldeten Benutzer |
| `fetch( 'explayouts', 'resolve_layout', hash() )` | das Layout der aktuellen Seite ([Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md)) |

Alle Fetch-Funktionen für Inhalte mit jedem Parameter:
[Referenz der Fetch-Funktionen](../erweiterungsentwicklung/teil-8-referenz/referenz-der-fetch-funktionen.md).

## Unterelemente und Teilbäume

Die Parameter von `list` und `tree` (`kernel/content/function_definition.php`): `parent_node_id`, `sort_by`, `offset`,
`limit`, `depth`, `depth_operator`, `class_filter_type`, `class_filter_array`, `attribute_filter`,
`extended_attribute_filter`, `main_node_only`, `ignore_visibility`, `language`, `only_translated`, `as_object`,
`load_data_map`, `objectname_filter`, `group_by` und `limitation`.

```smarty
{def $news = fetch( 'content', 'list', hash(
        'parent_node_id', $node.node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'article' ),
        'sort_by', array( 'published', false() ),
        'limit', 5 ) )}
{foreach $news as $item}
    {node_view_gui content_node=$item view='line'}
{/foreach}
{undef $news}
```

`list` gibt direkte Unterelemente zurück; `tree` geht den Teilbaum hinab (begrenzen Sie ihn mit `depth`).
`main_node_only` verhindert, dass ein Objekt zweimal erscheint, wenn es zwei Platzierungen unter demselben
übergeordneten Knoten hat.

## Nach Klasse und Attribut filtern

`class_filter_type` ist `include` oder `exclude`, mit `class_filter_array` aus Klassenbezeichnern. `attribute_filter`
ist eine Liste von Bedingungen `array( <field>, <operator>, <value> )`, optional beginnend mit `'and'` oder `'or'`; ein
Feld ist `<class>/<attribute>` oder eine Knoteneigenschaft wie `published`, `modified`, `section` oder `name`:

```smarty
{def $upcoming = fetch( 'content', 'tree', hash(
        'parent_node_id', $node.node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'exp_event' ),
        'attribute_filter', array( array( 'exp_event/start_date', '>=', currentdate() ) ),
        'sort_by', array( 'attribute', true(), 'exp_event/start_date' ),
        'limit', 10 ) )}
```

Operatoren und Beispiele: [Fetch-Filter und Sortierung](../designer/vorlagen/fetch-filter-und-sortierung.md#attributfilter).
Ein Template, das mit `currentdate()` vergleicht und im View-Cache liegt, behält die Zeit der Zwischenspeicherung;
geben Sie einer solchen Seite eine kurze Cache-Lebensdauer ([Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md)).

## Sortieren

`sort_by` ist `array( <field>, <ascending> )` oder eine Liste solcher Paare; zu den Feldern gehören `published`,
`modified`, `name`, `priority`, `path`, `class_identifier` und `attribute` (mit dem Attribut als drittem Element). Um
der Reihenfolge zu folgen, die Redakteure für einen Ordner gewählt haben, übergeben Sie die eigene Reihenfolge des
Knotens: `'sort_by', $node.sort_array`.

## Blättern

Der Offset einer Liste kommt aus dem View-Parameter `(offset)`:

```smarty
{def $limit = 10
     $total = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )
     $page = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                              'offset', $view_parameters.offset,
                                              'limit', $limit,
                                              'sort_by', $node.sort_array ) )}
{foreach $page as $item}{node_view_gui content_node=$item view='line'}{/foreach}
{include name=navigator uri='design:navigator/google.tpl'
         page_uri=$node.url_alias item_count=$total
         view_parameters=$view_parameters item_limit=$limit}
```

Die mitgelieferten Navigatoren liegen in `design/standard/templates/navigator/` ([Seitenblättern](../designer/vorlagen/seitenumbruch.md)).

## Regeln für schnelle Fetches

- Übergeben Sie bei Listen, die wachsen können, immer ein `limit`.
- Filtern Sie im Fetch nach Klasse, nicht mit `{if}` in der Schleife.
- Vermeiden Sie Fetches in Schleifen über Fetch-Ergebnisse; rufen Sie einmal ab oder verwenden Sie Relationen
  ([Die Schleifenfalle](../designer/vorlagen/fetch-leistung.md#die-schleifenfalle)).
- Verwenden Sie für Zählungen `list_count`, statt eine volle Liste zu zählen.
- Legen Sie Fetches, die auf jeder Seite gleich sind (Menüs, Fußzeilen), in einen Cache-Block
  ([Cache-Block](../designer/vorlagen/cache-block.md)).
- Attributfilter auf großen Bäumen sind SQL-Joins; messen Sie mit der SQL-Liste der Debug-Ausgabe an einer Kopie mit
  echten Daten.

## Wenn ein Fetch nicht genügt

Beantwortet keine Fetch-Funktion Ihre Frage, schreiben Sie eine in Ihrer Erweiterung: eine PHP-Methode, die Daten
zurückgibt, deklariert in der `function_definition.php` Ihres Moduls und aus jedem Template aufrufbar
([Fetch-Funktionen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/fetch-funktionen.md),
[Eine Fetch-Funktion von Hand](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tutorials/von-hand-fetch-funktion.md)).
Für komplexe Filter lässt `extended_attribute_filter` eine PHP-Klasse SQL-Bedingungen hinzufügen; siehe
`settings/extendedattributefilter.ini`.

## Verwandte Seiten

- [Inhalte aus PHP](inhalte-aus-php.md): dieselben Abfragen in PHP
- [Fetch für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/fetch-fuer-builder.md)
- [Modul 5: Fetch-Funktionen](../trainer/entwickler/modul-05-fetch-funktionen/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
