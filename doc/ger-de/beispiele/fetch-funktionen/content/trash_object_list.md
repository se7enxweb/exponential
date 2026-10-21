---
title: fetch( 'content', 'trash_object_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion trash_object_list des Moduls content: 6 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, trash_object_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'trash_object_list' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchTrashObjectList()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |
| `objectname_filter` | string | nein | `null` |
| `attribute_filter` | mixed | nein | `false` |
| `sort_by` | mixed | nein | `false` |
| `as_object` | bool | nein | `true` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'trash_object_list', hash( 'offset', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'trash_object_list', hash( 'offset', 1,
                                                    'limit', 1,
                                                    'objectname_filter', 'objectname_filter',
                                                    'attribute_filter', $attribute_filter,
                                                    'sort_by', $sort_by,
                                                    'as_object', true() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'trash_object_list', array( 'offset' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
