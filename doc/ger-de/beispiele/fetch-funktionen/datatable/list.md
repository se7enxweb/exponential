---
title: fetch( 'datatable', 'list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion list des Moduls datatable: 5 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, datatable, list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'datatable', 'list' )

Definiert in `kernel/datatable/function_definition.php`; sie ruft `expDataTableFunctionCollection::fetchList()` auf (`kernel/classes/datatable/expdatatablefunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class` | string | ja |  |
| `conditions` | array | nein | `false` |
| `sort` | array | nein | `false` |
| `offset` | integer | nein | `0` |
| `limit` | integer | nein | `50` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'datatable', 'list', hash( 'class', 'class' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'datatable', 'list', hash( 'class', 'class',
                                         'conditions', array(),
                                         'sort', array(),
                                         'offset', 0,
                                         'limit', 50 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'datatable', 'list', array( 'class' => 'class' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von datatable](README.md)
- [Die Ansichten des Moduls datatable](../../modulansichten/datatable/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/datatable/function_definition.php`, `kernel/classes/datatable/expdatatablefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
