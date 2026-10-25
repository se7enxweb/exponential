---
title: fetch( 'datatable', 'pointing_at' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion pointing_at des Moduls datatable: 2 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, datatable, pointing_at, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'datatable', 'pointing_at' )

Definiert in `kernel/datatable/function_definition.php`; sie ruft `expDataTableFunctionCollection::fetchPointingAt()` auf (`kernel/classes/datatable/expdatatablefunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_id` | integer | ja |  |
| `node_id` | integer | nein | `0` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'datatable', 'pointing_at', hash( 'object_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'datatable', 'pointing_at', hash( 'object_id', 1,
                                                'node_id', 0 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'datatable', 'pointing_at', array( 'object_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von datatable](README.md)
- [Die Ansichten des Moduls datatable](../../modulansichten/datatable/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/datatable/function_definition.php`, `kernel/classes/datatable/expdatatablefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
