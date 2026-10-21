---
title: fetch( 'layout', 'sitedesign_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion sitedesign_list des Moduls layout: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, layout, sitedesign_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'layout', 'sitedesign_list' )

Definiert in `kernel/layout/function_definition.php`; sie ruft `eZLayoutFunctionCollection::fetchSitedesignList()` auf (`kernel/layout/ezlayoutfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'layout', 'sitedesign_list' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'layout', 'sitedesign_list', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von layout](README.md)
- [Die Ansichten des Moduls layout](../../modulansichten/layout/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/layout/function_definition.php`, `kernel/layout/ezlayoutfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
