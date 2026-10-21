---
title: fetch( 'url', 'list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion list des Moduls url: 4 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, url, list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'url', 'list' )

Definiert in `kernel/url/function_definition.php`; sie ruft `eZURLFunctionCollection::fetchList()` auf (`kernel/url/ezurlfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `is_valid` |  | nein | `null` |
| `offset` |  | nein | `false` |
| `limit` |  | nein | `false` |
| `only_published` |  | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'url', 'list', hash( 'is_valid', $is_valid ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'url', 'list', hash( 'is_valid', $is_valid,
                                   'offset', $offset,
                                   'limit', $limit,
                                   'only_published', $only_published ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'url', 'list', array( 'is_valid' => $is_valid ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/standard/templates/url/view/all.tpl` Zeile 6:

```tpl
{set $url_list=fetch( 'url', 'list', hash( offset, $view_parameters.offset, limit, $url_limit, only_published, true() ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von url](README.md)
- [Die Ansichten des Moduls url](../../modulansichten/url/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/url/function_definition.php`, `kernel/url/ezurlfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
