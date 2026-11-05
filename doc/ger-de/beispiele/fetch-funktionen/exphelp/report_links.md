---
title: fetch( 'exphelp', 'report_links' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion report_links des Moduls exphelp: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, exphelp, report_links, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'exphelp', 'report_links' )

Definiert in `kernel/exphelp/function_definition.php`; sie ruft `expHelpFunctionCollection::fetchReportLinks()` auf (`kernel/classes/help/exphelpfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'exphelp', 'report_links' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'exphelp', 'report_links', array() );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/exphelp/search.tpl` Zeile 197:

```tpl
<a class="a4-btn" href={fetch( 'exphelp', 'report_links' ).url|ezurl}><span class="a4-icon a4-bi-exclamation-diamond" aria-hidden="true"></span>{'Missing a page? Tell us'|i18n( $help_ctx )}</a></p>
```

## Siehe auch

- [Alle Fetch-Funktionen von exphelp](README.md)
- [Die Ansichten des Moduls exphelp](../../modulansichten/exphelp/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/exphelp/function_definition.php`, `kernel/classes/help/exphelpfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
