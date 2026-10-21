---
title: fetch( 'shop', 'basket' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion basket des Moduls shop: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, shop, basket, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'shop', 'basket' )

Definiert in `kernel/shop/function_definition.php`; sie ruft `eZShopFunctionCollection::fetchBasket()` auf (`kernel/shop/ezshopfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'shop', 'basket' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'shop', 'basket', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von shop](README.md)
- [Die Ansichten des Moduls shop](../../modulansichten/shop/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/function_definition.php`, `kernel/shop/ezshopfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
