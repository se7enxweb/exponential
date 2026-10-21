---
title: fetch( 'content', 'assigned_node_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion assigned_node_count des Moduls content: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, assigned_node_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'assigned_node_count' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchAssignedNodeCount()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_id` | integer | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'assigned_node_count', hash( 'object_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'assigned_node_count', array( 'object_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/window_controls.tpl` Zeile 107:

```tpl
<span class="disabled" title="{'Tab is disabled, enable with toggler to the left of these tabs.'|i18n( 'design/admin/node/view/full' )}">{'Locations (%count)'|i18n( 'design/admin/node/view/full',, hash( '%count', fetch( 'content', 'assigned_node_count', hash( 'object_id', $node.object.id ) ) ) )}</span>
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
