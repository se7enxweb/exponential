---
title: fetch( 'setup', 'major_version' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion major_version des Moduls setup: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, setup, major_version, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'setup', 'major_version' )

Definiert in `kernel/setup/function_definition.php`; sie ruft `eZSetupFunctionCollection::fetchMajorVersion()` auf (`kernel/setup/ezsetupfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'setup', 'major_version' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'setup', 'major_version', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von setup](README.md)
- [Die Ansichten des Moduls setup](../../modulansichten/setup/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/function_definition.php`, `kernel/setup/ezsetupfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
