---
title: fetch( 'search', 'list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion list des Moduls search: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, search, list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'search', 'list' )

Definiert in `kernel/search/function_definition.php`; sie ruft `eZSearchFunctionCollection::fetchSearchList()` auf (`kernel/search/ezsearchfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'search', 'list', hash( 'offset', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'search', 'list', hash( 'offset', 1,
                                      'limit', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'search', 'list', array( 'offset' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von search](README.md)
- [Die Ansichten des Moduls search](../../modulansichten/suche/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/search/function_definition.php`, `kernel/search/ezsearchfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
