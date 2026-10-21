---
title: fetch( 'shop', 'best_sell_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion best_sell_list des Moduls shop: 8 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, shop, best_sell_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'shop', 'best_sell_list' )

Definiert in `kernel/shop/function_definition.php`; sie ruft `eZShopFunctionCollection::fetchBestSellList()` auf (`kernel/shop/ezshopfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `top_parent_node_id` | integer | ja |  |
| `limit` | integer | nein |  |
| `offset` | integer | nein | `false` |
| `start_time` | integer | nein | `false` |
| `end_time` | integer | nein | `false` |
| `duration` | integer | nein | `false` |
| `ascending` | boolean | nein | `false` |
| `extended` | boolean | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'shop', 'best_sell_list', hash( 'top_parent_node_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'shop', 'best_sell_list', hash( 'top_parent_node_id', 1,
                                              'limit', 1,
                                              'offset', 1,
                                              'start_time', 1,
                                              'end_time', 1,
                                              'duration', 1,
                                              'ascending', true(),
                                              'extended', true() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'shop', 'best_sell_list', array( 'top_parent_node_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von shop](README.md)
- [Die Ansichten des Moduls shop](../../modulansichten/shop/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/function_definition.php`, `kernel/shop/ezshopfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
