---
title: fetch( 'search', 'list_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion list_count des Moduls search: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, search, list_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'search', 'list_count' )

Definiert in `kernel/search/function_definition.php`; sie ruft `eZSearchFunctionCollection::fetchSearchListCount()` auf (`kernel/search/ezsearchfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'search', 'list_count' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'search', 'list_count', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von search](README.md)
- [Die Ansichten des Moduls search](../../modulansichten/suche/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/search/function_definition.php`, `kernel/search/ezsearchfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
