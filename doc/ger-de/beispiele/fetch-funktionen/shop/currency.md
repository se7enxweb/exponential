---
title: fetch( 'shop', 'currency' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion currency des Moduls shop: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, shop, currency, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'shop', 'currency' )

Definiert in `kernel/shop/function_definition.php`; sie ruft `eZShopFunctionCollection::fetchCurrency()` auf (`kernel/shop/ezshopfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `code` | string | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'shop', 'currency', hash( 'code', 'code' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'shop', 'currency', array( 'code' => 'code' ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/shop/archivelist.tpl` Zeile 63:

```tpl
{set $currency = fetch( 'shop', 'currency', hash( 'code', $Orders.item.productcollection.currency_code ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von shop](README.md)
- [Die Ansichten des Moduls shop](../../modulansichten/shop/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/function_definition.php`, `kernel/shop/ezshopfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
