---
title: fetch( 'content', 'search' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion search des Moduls content: 12 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, search, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'search' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchContentSearch()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `text` | string | ja |  |
| `subtree_array` | array | nein | `false` |
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |
| `publish_timestamp` | mixed | nein | `false` |
| `publish_date` | integer | nein | `false` |
| `section_id` | integer | nein | `false` |
| `class_id` | integer | nein | `false` |
| `class_attribute_id` | integer | nein | `false` |
| `ignore_visibility` | bool | nein | `null` |
| `limitation` | array | nein | `null` |
| `sort_by` | mixed | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'search', hash( 'text', 'text' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'search', hash( 'text', 'text',
                                         'subtree_array', array(),
                                         'offset', 1,
                                         'limit', 1,
                                         'publish_timestamp', $publish_timestamp,
                                         'publish_date', 1,
                                         'section_id', 1,
                                         'class_id', 1,
                                         'class_attribute_id', 1,
                                         'ignore_visibility', true(),
                                         'limitation', array(),
                                         'sort_by', $sort_by ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'search', array( 'text' => 'text' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
