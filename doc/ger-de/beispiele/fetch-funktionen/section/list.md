---
title: fetch( 'section', 'list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion list des Moduls section: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, section, list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'section', 'list' )

Definiert in `kernel/section/function_definition.php`; sie ruft `eZSectionFunctionCollection::fetchSectionList()` auf (`kernel/section/ezsectionfunctioncollection.php`).

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'section', 'list' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'section', 'list', array() );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/page_search.tpl` Zeile 68:

```tpl
{foreach fetch( 'section', 'list' ) as $section}
```

## Siehe auch

- [Alle Fetch-Funktionen von section](README.md)
- [Die Ansichten des Moduls section](../../modulansichten/section/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/section/function_definition.php`, `kernel/section/ezsectionfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
