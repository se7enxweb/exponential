---
title: fetch( 'content', 'country_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion country_list des Moduls content: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, country_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'country_list' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchCountryList()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `filter` | string | nein |  |
| `value` | string | nein |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'country_list', hash( 'filter', 'filter' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'country_list', hash( 'filter', 'filter',
                                               'value', 'value' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'country_list', array( 'filter' => 'filter' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
