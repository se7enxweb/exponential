---
title: fetch( 'content', 'version' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion version des Moduls content: 2 Parameter, 2 erforderlich."
keywords: Fetch-Funktion, fetch, content, version, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'version' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchContentVersion()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_id` | integer | ja |  |
| `version_id` | integer | ja | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'version', hash( 'object_id', 1,
                                          'version_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'version', array( 'object_id' => 1, 'version_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
