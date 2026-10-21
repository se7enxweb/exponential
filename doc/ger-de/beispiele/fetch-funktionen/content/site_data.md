---
title: fetch( 'content', 'site_data' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion site_data des Moduls content: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, site_data, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'site_data' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchSiteData()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `name` | string | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'site_data', hash( 'name', 'name' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'site_data', array( 'name' => 'name' ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/setup/sitedata.tpl` Zeile 18:

```tpl
<p>{"Templates can read a row with fetch( 'content', 'site_data', hash( 'name', ... ) ) only when its name is listed in site.ini [SiteDataSettings] TemplateReadable[]."|i18n( 'design/admin/setup/sitedata' )}&nbsp;
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
