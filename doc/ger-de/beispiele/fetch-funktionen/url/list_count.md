---
title: fetch( 'url', 'list_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion list_count des Moduls url: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, url, list_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'url', 'list_count' )

Definiert in `kernel/url/function_definition.php`; sie ruft `eZURLFunctionCollection::fetchListCount()` auf (`kernel/url/ezurlfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `is_valid` |  | nein | `null` |
| `only_published` |  | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'url', 'list_count', hash( 'is_valid', $is_valid ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'url', 'list_count', hash( 'is_valid', $is_valid,
                                         'only_published', $only_published ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'url', 'list_count', array( 'is_valid' => $is_valid ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von url](README.md)
- [Die Ansichten des Moduls url](../../modulansichten/url/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/url/function_definition.php`, `kernel/url/ezurlfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
