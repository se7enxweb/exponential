---
title: fetch( 'package', 'list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion list des Moduls package: 4 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, package, list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'package', 'list' )

Definiert in `kernel/package/function_definition.php`; sie ruft `eZPackageFunctionCollection::fetchList()` auf (`kernel/package/ezpackagefunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `filter_array` | array | nein | `false` |
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |
| `repository_id` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'package', 'list', hash( 'filter_array', array() ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'package', 'list', hash( 'filter_array', array(),
                                       'offset', 1,
                                       'limit', 1,
                                       'repository_id', 'repository_id' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'package', 'list', array( 'filter_array' => array() ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von package](README.md)
- [Die Ansichten des Moduls package](../../modulansichten/package/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/function_definition.php`, `kernel/package/ezpackagefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
