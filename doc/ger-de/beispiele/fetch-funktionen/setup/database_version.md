---
title: fetch( 'setup', 'database_version' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion database_version des Moduls setup: 1 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, setup, database_version, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'setup', 'database_version' )

Definiert in `kernel/setup/function_definition.php`; sie ruft `eZSetupFunctionCollection::fetchDatabaseVersion()` auf (`kernel/setup/ezsetupfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `with_release` | bool | nein | `true` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'setup', 'database_version', hash( 'with_release', true() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'setup', 'database_version', array( 'with_release' => true ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von setup](README.md)
- [Die Ansichten des Moduls setup](../../modulansichten/setup/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/function_definition.php`, `kernel/setup/ezsetupfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
