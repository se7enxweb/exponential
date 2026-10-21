---
title: fetch( 'content', 'assigned_nodes' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion assigned_nodes des Moduls content: 5 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, assigned_nodes, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'assigned_nodes' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchAssignedNodes()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_id` | integer | ja |  |
| `offset` | integer | nein | `0` |
| `limit` | integer | nein | `false` |
| `sort_field` | string | nein | `false` |
| `sort_order` | string | nein | `'asc'` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'assigned_nodes', hash( 'object_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'assigned_nodes', hash( 'object_id', 1,
                                                 'offset', 0,
                                                 'limit', 1,
                                                 'sort_field', 'sort_field',
                                                 'sort_order', 'asc' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'assigned_nodes', array( 'object_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
