---
title: fetch( 'uiswitch', 'toolbar' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion toolbar des Moduls uiswitch: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, uiswitch, toolbar, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'uiswitch', 'toolbar' )

Definiert in `kernel/uiswitch/function_definition.php`; sie ruft `expUISwitch::fetchToolbar()` auf (`kernel/classes/uiswitch/expuiswitch.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'uiswitch', 'toolbar' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'uiswitch', 'toolbar', array() );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/toolbar/full/admin_ui_switch.tpl` Zeile 7:

```tpl
{def $exp_ui_choices = fetch( 'uiswitch', 'toolbar' )}
```

## Siehe auch

- [Alle Fetch-Funktionen von uiswitch](README.md)
- [Die Ansichten des Moduls uiswitch](../../modulansichten/uiswitch/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/uiswitch/function_definition.php`, `kernel/classes/uiswitch/expuiswitch.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
