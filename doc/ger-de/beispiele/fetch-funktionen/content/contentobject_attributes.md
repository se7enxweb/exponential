---
title: fetch( 'content', 'contentobject_attributes' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion contentobject_attributes des Moduls content: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, contentobject_attributes, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'contentobject_attributes' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::contentobjectAttributes()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `version` | object | nein | `0` |
| `language_code` | string | nein | `''` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'contentobject_attributes', hash( 'version', 0 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'contentobject_attributes', hash( 'version', 0,
                                                           'language_code', 'language_code' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'contentobject_attributes', array( 'version' => $version ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
