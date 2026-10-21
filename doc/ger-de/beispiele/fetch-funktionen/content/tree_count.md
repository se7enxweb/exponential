---
title: fetch( 'content', 'tree_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion tree_count des Moduls content: 13 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, tree_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'tree_count' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchObjectTreeCount()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `parent_node_id` | integer | ja |  |
| `only_translated` | bool | nein | `false` |
| `language` | string | nein | `false` |
| `class_filter_type` | string | nein | `false` |
| `class_filter_array` | array | nein | `false` |
| `attribute_filter` | mixed | nein | `false` |
| `depth` | string | nein | `false` |
| `depth_operator` | string | nein | `false` |
| `ignore_visibility` | bool | nein | `false` |
| `limitation` | array | nein | `null` |
| `main_node_only` | bool | nein | `false` |
| `extended_attribute_filter` | mixed | nein | `false` |
| `objectname_filter` | string | nein | `null` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'tree_count', hash( 'parent_node_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'tree_count', hash( 'parent_node_id', 1,
                                             'only_translated', true(),
                                             'language', 'language',
                                             'class_filter_type', 'class_filter_type',
                                             'class_filter_array', array(),
                                             'attribute_filter', $attribute_filter,
                                             'depth', 'depth',
                                             'depth_operator', 'depth_operator',
                                             'ignore_visibility', true(),
                                             'limitation', array(),
                                             'main_node_only', true(),
                                             'extended_attribute_filter', $extended_attribute_filter,
                                             'objectname_filter', 'objectname_filter' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'tree_count', array( 'parent_node_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin4/templates/content/dashboard.tpl` Zeile 55:

```tpl
{set $day_count = fetch( 'content', 'tree_count', hash( 'parent_node_id', 1, 'attribute_filter', array( array( 'published', 'between', array( $day_from, sum( $day_from, 86399 ) ) ) ) ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
