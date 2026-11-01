---
title: fetch( 'staticpublish', 'summary' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion summary des Moduls staticpublish: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, staticpublish, summary, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'staticpublish', 'summary' )

Definiert in `kernel/staticpublish/function_definition.php`; sie ruft `expStaticAdmin::fetchSummary()` auf (`kernel/classes/staticpublish/expstaticadmin.php`).

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'staticpublish', 'summary' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'staticpublish', 'summary', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von staticpublish](README.md)
- [Die Ansichten des Moduls staticpublish](../../modulansichten/staticpublish/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/staticpublish/function_definition.php`, `kernel/classes/staticpublish/expstaticadmin.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
