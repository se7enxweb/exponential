---
title: fetch( 'content', 'version_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion version_list des Moduls content: 4 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, version_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'version_list' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchVersionList()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `contentobject` | object | ja |  |
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |
| `sorts` | array | nein | `null` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'version_list', hash( 'contentobject', $contentobject ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'version_list', hash( 'contentobject', $contentobject,
                                               'offset', 1,
                                               'limit', 1,
                                               'sorts', array() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'version_list', array( 'contentobject' => $contentobject ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
