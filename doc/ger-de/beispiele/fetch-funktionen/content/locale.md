---
title: fetch( 'content', 'locale' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion locale des Moduls content: 1 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, locale, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'locale' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchLocale()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `locale_code` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'locale', hash( 'locale_code', 'locale_code' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'locale', array( 'locale_code' => 'locale_code' ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/class/edit.tpl` Zeile 73:

```tpl
{def $locale = fetch( 'content', 'locale', hash( 'locale_code', $language_code ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
