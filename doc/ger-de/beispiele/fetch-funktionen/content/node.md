---
title: fetch( 'content', 'node' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion node des Moduls content: 4 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, node, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'node' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchContentNode()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `node_id` | integer | nein | `false` |
| `node_path` | string | nein | `false` |
| `language_code` | string | nein | `false` |
| `remote_id` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'node', hash( 'node_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'node', hash( 'node_id', 1,
                                       'node_path', 'node_path',
                                       'language_code', 'language_code',
                                       'remote_id', 'remote_id' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'node', array( 'node_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/content/browse_mode_list.tpl` Zeile 2:

```tpl
{def $swap_node = fetch( 'content', 'node', hash( 'node_id', $browse.persistent_data.ContentNodeID ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
