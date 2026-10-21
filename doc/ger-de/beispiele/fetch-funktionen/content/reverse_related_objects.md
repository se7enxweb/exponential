---
title: fetch( 'content', 'reverse_related_objects' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion reverse_related_objects des Moduls content: 10 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, reverse_related_objects, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'reverse_related_objects' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchReverseRelatedObjects()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_id` | integer | ja |  |
| `attribute_identifier` | string | nein |  |
| `all_relations` | boolean | nein | `false` |
| `group_by_attribute` | boolean | nein | `false` |
| `sort_by` | array | nein | `array()` |
| `ignore_visibility` | boolean | nein | `null` |
| `limit` | integer | nein | `false` |
| `offset` | integer | nein | `false` |
| `as_object` | boolean | nein | `true` |
| `load_data_map` | boolean | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'reverse_related_objects', hash( 'object_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'reverse_related_objects', hash( 'object_id', 1,
                                                          'attribute_identifier', 'attribute_identifier',
                                                          'all_relations', true(),
                                                          'group_by_attribute', true(),
                                                          'sort_by', array(),
                                                          'ignore_visibility', true(),
                                                          'limit', 1,
                                                          'offset', 1,
                                                          'as_object', true(),
                                                          'load_data_map', true() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'reverse_related_objects', array( 'object_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/relations.tpl` Zeile 79:

```tpl
{def $reverse_related_objects_grouped = fetch( 'content', 'reverse_related_objects', hash( 'object_id', $node.object.id, 'all_relations', true(), 'group_by_attribute', true(), 'sort_by', array( array( 'class_identifier', true() ), array( 'name', true() ) ), 'limit', $page_limit, 'offset', $offset ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
