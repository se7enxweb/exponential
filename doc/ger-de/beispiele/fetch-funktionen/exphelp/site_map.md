---
title: fetch( 'exphelp', 'site_map' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion site_map des Moduls exphelp: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, exphelp, site_map, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'exphelp', 'site_map' )

Definiert in `kernel/exphelp/function_definition.php`; sie ruft `expHelpFunctionCollection::fetchSiteMap()` auf (`kernel/classes/help/exphelpfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_id` | integer | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'exphelp', 'site_map', hash( 'object_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'exphelp', 'site_map', array( 'object_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `extension/exphelpsite/design/standard/override/templates/help/full_map.tpl` Zeile 7:

```tpl
{def $help = fetch( 'exphelp', 'site_map', hash( 'object_id', $node.contentobject_id ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von exphelp](README.md)
- [Die Ansichten des Moduls exphelp](../../modulansichten/exphelp/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/exphelp/function_definition.php`, `kernel/classes/help/exphelpfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
