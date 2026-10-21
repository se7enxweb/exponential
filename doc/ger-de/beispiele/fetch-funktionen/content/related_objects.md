---
title: fetch( 'content', 'related_objects' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion related_objects des Moduls content: 11 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, related_objects, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'related_objects' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchRelatedObjects()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_id` | integer | ja |  |
| `attribute_identifier` | string | nein | `'0'` |
| `all_relations` | boolean | nein | `false` |
| `group_by_attribute` | boolean | nein | `false` |
| `sort_by` | array | nein | `array()` |
| `limit` | integer | nein | `false` |
| `offset` | integer | nein | `false` |
| `as_object` | boolean | nein | `true` |
| `load_data_map` | boolean | nein | `false` |
| `ignore_visibility` | boolean | nein | `null` |
| `related_class_identifiers` | array | nein | `null` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'related_objects', hash( 'object_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'related_objects', hash( 'object_id', 1,
                                                  'attribute_identifier', '0',
                                                  'all_relations', true(),
                                                  'group_by_attribute', true(),
                                                  'sort_by', array(),
                                                  'limit', 1,
                                                  'offset', 1,
                                                  'as_object', true(),
                                                  'load_data_map', true(),
                                                  'ignore_visibility', true(),
                                                  'related_class_identifiers', array() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'related_objects', array( 'object_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/relations.tpl` Zeile 20:

```tpl
{def $related_objects_grouped = fetch( 'content', 'related_objects', hash( 'object_id', $node.object.id, 'all_relations', true(), 'group_by_attribute', true(), 'sort_by', array( array( 'class_identifier', true() ), array( 'name', true() ) ), 'offset', $offset, 'limit', $page_limit ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
