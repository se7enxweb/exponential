---
title: fetch( 'apikey', 'can_create' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion can_create des Moduls apikey: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, apikey, can_create, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'apikey', 'can_create' )

Definiert in `kernel/apikey/function_definition.php`; sie ruft `expApiKeyFunctionCollection::fetchCanCreate()` auf (`kernel/classes/apikey/expapikeyfunctioncollection.php`).

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'apikey', 'can_create' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'apikey', 'can_create', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von apikey](README.md)
- [Die Ansichten des Moduls apikey](../../modulansichten/apikey/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/apikey/function_definition.php`, `kernel/classes/apikey/expapikeyfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
