---
title: fetch( 'datatable', 'can' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion can des Moduls datatable: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, datatable, can, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'datatable', 'can' )

Definiert in `kernel/datatable/function_definition.php`; sie ruft `expDataTableFunctionCollection::fetchCan()` auf (`kernel/classes/datatable/expdatatablefunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `function` | string | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'datatable', 'can', hash( 'function', 'function' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'datatable', 'can', array( 'function' => 'function' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von datatable](README.md)
- [Die Ansichten des Moduls datatable](../../modulansichten/datatable/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/datatable/function_definition.php`, `kernel/classes/datatable/expdatatablefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
