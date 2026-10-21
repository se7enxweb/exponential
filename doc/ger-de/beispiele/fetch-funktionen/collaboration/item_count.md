---
title: fetch( 'collaboration', 'item_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion item_count des Moduls collaboration: 4 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, collaboration, item_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'collaboration', 'item_count' )

Definiert in `kernel/collaboration/function_definition.php`; sie ruft `eZCollaborationFunctionCollection::fetchItemCount()` auf (`kernel/collaboration/ezcollaborationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `is_read` |  | nein | `null` |
| `is_active` |  | nein | `null` |
| `parent_group_id` |  | nein | `null` |
| `status` |  | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'collaboration', 'item_count', hash( 'is_read', $is_read ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'collaboration', 'item_count', hash( 'is_read', $is_read,
                                                   'is_active', $is_active,
                                                   'parent_group_id', $parent_group_id,
                                                   'status', $status ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'collaboration', 'item_count', array( 'is_read' => $is_read ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/collaboration/group/view/list.tpl` Zeile 6:

```tpl
{let  group_item_count=fetch("collaboration","item_count",hash("parent_group_id",$collab_group.id))}
```

## Siehe auch

- [Alle Fetch-Funktionen von collaboration](README.md)
- [Die Ansichten des Moduls collaboration](../../modulansichten/collaboration/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/function_definition.php`, `kernel/collaboration/ezcollaborationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
