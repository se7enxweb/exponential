---
title: fetch( 'content', 'object' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion object des Moduls content: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, object, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'object' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchContentObject()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_id` | integer | nein | `false` |
| `remote_id` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'object', hash( 'object_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'object', hash( 'object_id', 1,
                                         'remote_id', 'remote_id' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'object', array( 'object_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/content/trash.tpl` Zeile 208:

```tpl
<dd>{if $item.owner_name}<a href={concat( '/content/view/full/', fetch( 'content', 'object', hash( 'object_id', $item.owner_id ) ).main_node_id )|ezurl} dir="auto">{$item.owner_name|wash}</a>{else}{'Unknown'|i18n( 'design/admin/content/trash' )}{/if}</dd>
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
