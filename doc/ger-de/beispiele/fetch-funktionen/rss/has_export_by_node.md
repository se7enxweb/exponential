---
title: fetch( 'rss', 'has_export_by_node' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion has_export_by_node des Moduls rss: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, rss, has_export_by_node, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'rss', 'has_export_by_node' )

Definiert in `kernel/rss/function_definition.php`; sie ruft `eZRSSFunctionCollection::hasExportByNode()` auf (`kernel/rss/ezrssfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `node_id` | integer | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'rss', 'has_export_by_node', hash( 'node_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'rss', 'has_export_by_node', array( 'node_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/node/view/full.tpl` Zeile 46:

```tpl
{if fetch( 'rss', 'has_export_by_node', hash( 'node_id', $node.node_id ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von rss](README.md)
- [Die Ansichten des Moduls rss](../../modulansichten/rss/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/rss/function_definition.php`, `kernel/rss/ezrssfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
