---
title: fetch( 'content', 'access' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion access des Moduls content: 5 Parameter, 2 erforderlich."
keywords: Fetch-Funktion, fetch, content, access, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'access' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::checkAccess()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `access` | string | ja |  |
| `contentobject` | object | ja |  |
| `contentclass_id` | integer | nein | `false` |
| `parent_contentclass_id` | integer | nein | `false` |
| `language` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'access', hash( 'access', 'access',
                                         'contentobject', $contentobject ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'access', hash( 'access', 'access',
                                         'contentobject', $contentobject,
                                         'contentclass_id', 1,
                                         'parent_contentclass_id', 1,
                                         'language', 'language' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'access', array( 'access' => 'access', 'contentobject' => $contentobject ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
