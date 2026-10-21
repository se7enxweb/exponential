---
title: fetch( 'content', 'prioritized_language_codes' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion prioritized_language_codes des Moduls content: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, prioritized_language_codes, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'prioritized_language_codes' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchPrioritizedLanguageCodes()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'prioritized_language_codes' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'prioritized_language_codes', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
