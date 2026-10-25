---
title: fetch( 'datatable', 'row' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion row des Moduls datatable: 2 Parameter, 2 erforderlich."
keywords: Fetch-Funktion, fetch, datatable, row, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'datatable', 'row' )

Definiert in `kernel/datatable/function_definition.php`; sie ruft `expDataTableFunctionCollection::fetchRow()` auf (`kernel/classes/datatable/expdatatablefunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class` | string | ja |  |
| `key` | array | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'datatable', 'row', hash( 'class', 'class',
                                        'key', array() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'datatable', 'row', array( 'class' => 'class', 'key' => array() ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von datatable](README.md)
- [Die Ansichten des Moduls datatable](../../modulansichten/datatable/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/datatable/function_definition.php`, `kernel/classes/datatable/expdatatablefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
