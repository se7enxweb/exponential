---
title: fetch( 'content', 'calendar' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion calendar des Moduls content: 14 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, calendar, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'calendar' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::calendar()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `parent_node_id` | integer | ja |  |
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |
| `depth` | integer | nein | `1` |
| `depth_operator` | string | nein | `'le'` |
| `class_id` | integer | nein | `false` |
| `attribute_filter` | mixed | nein | `false` |
| `extended_attribute_filter` | mixed | nein | `false` |
| `class_filter_type` | string | nein | `false` |
| `class_filter_array` | array | nein | `false` |
| `group_by` | array | nein | `false` |
| `main_node_only` | bool | nein | `false` |
| `ignore_visibility` | bool | nein | `false` |
| `limitation` | array | nein | `null` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'calendar', hash( 'parent_node_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'calendar', hash( 'parent_node_id', 1,
                                           'offset', 1,
                                           'limit', 1,
                                           'depth', 1,
                                           'depth_operator', 'le',
                                           'class_id', 1,
                                           'attribute_filter', $attribute_filter,
                                           'extended_attribute_filter', $extended_attribute_filter,
                                           'class_filter_type', 'class_filter_type',
                                           'class_filter_array', array(),
                                           'group_by', array(),
                                           'main_node_only', true(),
                                           'ignore_visibility', true(),
                                           'limitation', array() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'calendar', array( 'parent_node_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
