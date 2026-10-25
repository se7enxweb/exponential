---
title: fetch( 'content', 'view_bookmark' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion view_bookmark des Moduls content: 2 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, view_bookmark, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'view_bookmark' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchViewBookmark()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `uri` | string | ja |  |
| `path` | array | nein | `array()` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'view_bookmark', hash( 'uri', 'uri' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'view_bookmark', hash( 'uri', 'uri',
                                                'path', array() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'view_bookmark', array( 'uri' => 'uri' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
