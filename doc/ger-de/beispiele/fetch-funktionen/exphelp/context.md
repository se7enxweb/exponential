---
title: fetch( 'exphelp', 'context' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion context des Moduls exphelp: 3 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, exphelp, context, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'exphelp', 'context' )

Definiert in `kernel/exphelp/function_definition.php`; sie ruft `expHelpFunctionCollection::fetchContext()` auf (`kernel/classes/help/exphelpfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `module` | string | nein | `''` |
| `view` | string | nein | `''` |
| `design` | string | nein | `''` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'exphelp', 'context', hash( 'module', 'module' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'exphelp', 'context', hash( 'module', 'module',
                                          'view', 'view',
                                          'design', 'design' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'exphelp', 'context', array( 'module' => 'module' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von exphelp](README.md)
- [Die Ansichten des Moduls exphelp](../../modulansichten/exphelp/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/exphelp/function_definition.php`, `kernel/classes/help/exphelpfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
