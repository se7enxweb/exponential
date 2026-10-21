---
title: fetch( 'shop', 'user_country' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion user_country des Moduls shop: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, shop, user_country, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'shop', 'user_country' )

Definiert in `kernel/shop/function_definition.php`; sie ruft `eZShopFunctionCollection::fetchUserCountry()` auf (`kernel/shop/ezshopfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'shop', 'user_country' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'shop', 'user_country', array() );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/standard/templates/toolbar/full/user_country.tpl` Zeile 8:

```tpl
{def $user_country=fetch( 'shop', 'user_country' )}
```

## Siehe auch

- [Alle Fetch-Funktionen von shop](README.md)
- [Die Ansichten des Moduls shop](../../modulansichten/shop/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/function_definition.php`, `kernel/shop/ezshopfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
