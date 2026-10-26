---
title: fetch( 'collaboration', 'tree_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion tree_count des Moduls collaboration: 4 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, collaboration, tree_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'collaboration', 'tree_count' )

Definiert in `kernel/collaboration/function_definition.php`; sie ruft `eZCollaborationFunctionCollection::fetchObjectTreeCount()` auf (`kernel/collaboration/ezcollaborationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `parent_node_id` |  | ja |  |
| `class_filter_type` |  | nein | `false` |
| `class_filter_array` |  | nein | `false` |
| `depth` |  | nein | `0` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'collaboration', 'tree_count', hash( 'parent_node_id', $parent_node_id ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'collaboration', 'tree_count', hash( 'parent_node_id', $parent_node_id,
                                                   'class_filter_type', $class_filter_type,
                                                   'class_filter_array', $class_filter_array,
                                                   'depth', 0 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'collaboration', 'tree_count', array( 'parent_node_id' => $parent_node_id ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von collaboration](README.md)
- [Die Ansichten des Moduls collaboration](../../modulansichten/collaboration/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/function_definition.php`, `kernel/collaboration/ezcollaborationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
