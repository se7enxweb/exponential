---
title: fetch( 'setup', 'extension_counts' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion extension_counts des Moduls setup: 1 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, setup, extension_counts, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'setup', 'extension_counts' )

Definiert in `kernel/setup/function_definition.php`; sie ruft `eZSetupFunctionCollection::fetchExtensionCounts()` auf (`kernel/setup/ezsetupfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `all_siteaccesses` | bool | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'setup', 'extension_counts', hash( 'all_siteaccesses', true() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'setup', 'extension_counts', array( 'all_siteaccesses' => true ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin4/templates/content/dashboard.tpl` Zeile 166:

```tpl
{def $extension_counts = fetch( 'setup', 'extension_counts' )}
```

## Siehe auch

- [Alle Fetch-Funktionen von setup](README.md)
- [Die Ansichten des Moduls setup](../../modulansichten/setup/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/function_definition.php`, `kernel/setup/ezsetupfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
