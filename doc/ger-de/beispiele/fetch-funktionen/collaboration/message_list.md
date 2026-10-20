---
title: fetch( 'collaboration', 'message_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion message_list des Moduls collaboration: 4 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, collaboration, message_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'collaboration', 'message_list' )

Definiert in `kernel/collaboration/function_definition.php`; sie ruft `eZCollaborationFunctionCollection::fetchMessageList()` auf (`kernel/collaboration/ezcollaborationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `item_id` |  | ja | `false` |
| `sort_by` |  | nein | `false` |
| `offset` |  | nein | `false` |
| `limit` |  | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'collaboration', 'message_list', hash( 'item_id', $item_id ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'collaboration', 'message_list', hash( 'item_id', $item_id,
                                                     'sort_by', $sort_by,
                                                     'offset', $offset,
                                                     'limit', $limit ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'collaboration', 'message_list', array( 'item_id' => $item_id ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von collaboration](README.md)
- [Die Ansichten des Moduls collaboration](../../modulansichten/collaboration/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/function_definition.php`, `kernel/collaboration/ezcollaborationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
