---
title: fetch( 'datatable', 'count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion count des Moduls datatable: 2 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, datatable, count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'datatable', 'count' )

Definiert in `kernel/datatable/function_definition.php`; sie ruft `expDataTableFunctionCollection::fetchCount()` auf (`kernel/classes/datatable/expdatatablefunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class` | string | ja |  |
| `conditions` | array | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'datatable', 'count', hash( 'class', 'class' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'datatable', 'count', hash( 'class', 'class',
                                          'conditions', array() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'datatable', 'count', array( 'class' => 'class' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von datatable](README.md)
- [Die Ansichten des Moduls datatable](../../modulansichten/datatable/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/datatable/function_definition.php`, `kernel/classes/datatable/expdatatablefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
