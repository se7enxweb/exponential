---
title: fetch( 'exphelp', 'license' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion license des Moduls exphelp: 1 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, exphelp, license, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'exphelp', 'license' )

Definiert in `kernel/exphelp/function_definition.php`; sie ruft `expHelpFunctionCollection::fetchLicense()` auf (`kernel/classes/help/exphelpfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `id` | string | nein | `''` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'exphelp', 'license', hash( 'id', 'id' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'exphelp', 'license', array( 'id' => 'id' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von exphelp](README.md)
- [Die Ansichten des Moduls exphelp](../../modulansichten/exphelp/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/exphelp/function_definition.php`, `kernel/classes/help/exphelpfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
