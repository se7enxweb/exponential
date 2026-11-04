---
title: fetch( 'uiswitch', 'front_designs' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion front_designs des Moduls uiswitch: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, uiswitch, front_designs, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'uiswitch', 'front_designs' )

Definiert in `kernel/uiswitch/function_definition.php`; sie ruft `expFrontDesignSwitch::fetchSwitch()` auf.
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'uiswitch', 'front_designs' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'uiswitch', 'front_designs', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von uiswitch](README.md)
- [Die Ansichten des Moduls uiswitch](../../modulansichten/uiswitch/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/uiswitch/function_definition.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
