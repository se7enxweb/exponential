---
title: fetch( 'collaboration', 'group_tree' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion group_tree des Moduls collaboration: 5 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, collaboration, group_tree, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'collaboration', 'group_tree' )

Definiert in `kernel/collaboration/function_definition.php`; sie ruft `eZCollaborationFunctionCollection::fetchGroupTree()` auf (`kernel/collaboration/ezcollaborationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `parent_group_id` |  | ja |  |
| `sort_by` |  | nein | `false` |
| `offset` |  | nein | `false` |
| `limit` |  | nein | `false` |
| `depth` |  | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'collaboration', 'group_tree', hash( 'parent_group_id', $parent_group_id ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'collaboration', 'group_tree', hash( 'parent_group_id', $parent_group_id,
                                                   'sort_by', $sort_by,
                                                   'offset', $offset,
                                                   'limit', $limit,
                                                   'depth', $depth ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'collaboration', 'group_tree', array( 'parent_group_id' => $parent_group_id ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von collaboration](README.md)
- [Die Ansichten des Moduls collaboration](../../modulansichten/collaboration/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/function_definition.php`, `kernel/collaboration/ezcollaborationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
