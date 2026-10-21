---
title: fetch( 'collaboration', 'item_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion item_list des Moduls collaboration: 7 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, collaboration, item_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'collaboration', 'item_list' )

Definiert in `kernel/collaboration/function_definition.php`; sie ruft `eZCollaborationFunctionCollection::fetchItemList()` auf (`kernel/collaboration/ezcollaborationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `sort_by` |  | nein | `false` |
| `offset` |  | nein | `false` |
| `limit` |  | nein | `false` |
| `status` |  | nein | `false` |
| `is_read` |  | nein | `null` |
| `is_active` |  | nein | `null` |
| `parent_group_id` |  | nein | `null` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'collaboration', 'item_list', hash( 'sort_by', $sort_by ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'collaboration', 'item_list', hash( 'sort_by', $sort_by,
                                                  'offset', $offset,
                                                  'limit', $limit,
                                                  'status', $status,
                                                  'is_read', $is_read,
                                                  'is_active', $is_active,
                                                  'parent_group_id', $parent_group_id ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'collaboration', 'item_list', array( 'sort_by' => $sort_by ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/collaboration/group/view/list.tpl` Zeile 23:

```tpl
{include uri="design:collaboration/item_list.tpl" item_list=fetch( "collaboration", "item_list", hash( "limit", $item_limit, "offset", $offset, "parent_group_id", $collab_group.id ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von collaboration](README.md)
- [Die Ansichten des Moduls collaboration](../../modulansichten/collaboration/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/function_definition.php`, `kernel/collaboration/ezcollaborationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
