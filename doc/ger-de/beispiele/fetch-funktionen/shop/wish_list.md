---
title: fetch( 'shop', 'wish_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion wish_list des Moduls shop: 3 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, shop, wish_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'shop', 'wish_list' )

Definiert in `kernel/shop/function_definition.php`; sie ruft `eZShopFunctionCollection::fetchWishList()` auf (`kernel/shop/ezshopfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `production_id` | integer | ja |  |
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'shop', 'wish_list', hash( 'production_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'shop', 'wish_list', hash( 'production_id', 1,
                                         'offset', 1,
                                         'limit', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'shop', 'wish_list', array( 'production_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/shop/wishlist.tpl` Zeile 61:

```tpl
{section var=WishedItems loop=fetch( 'shop', 'wish_list', hash( 'production_id', $wish_list.productcollection_id, 'offset', $view_parameters.offset, 'limit', number_of_items ) ) sequence=array( bglight, bgdark )}
```

## Siehe auch

- [Alle Fetch-Funktionen von shop](README.md)
- [Die Ansichten des Moduls shop](../../modulansichten/shop/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/function_definition.php`, `kernel/shop/ezshopfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
