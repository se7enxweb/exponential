---
title: fetch( 'package', 'can_remove' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion can_remove des Moduls package: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, package, can_remove, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'package', 'can_remove' )

Definiert in `kernel/package/function_definition.php`; sie ruft `eZPackageFunctionCollection::canRemove()` auf (`kernel/package/ezpackagefunctioncollection.php`).

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'package', 'can_remove' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'package', 'can_remove', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von package](README.md)
- [Die Ansichten des Moduls package](../../modulansichten/package/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/function_definition.php`, `kernel/package/ezpackagefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
