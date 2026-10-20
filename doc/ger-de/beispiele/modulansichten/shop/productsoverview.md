---
title: /shop/productsoverview
audience: developer, admin
category: examples
summary: "Die Ansicht shop/productsoverview: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, shop, productsoverview, shop/productsoverview, module, view, URL
type: generated
generated: exp:help:examples
---
# /shop/productsoverview

Eine Ansicht des Moduls `shop`, definiert in `kernel/shop/module.php` und ausgeführt von `kernel/shop/productsoverview.php`.

## Adresse

```text
/shop/productsoverview/(product_class)/<ProductClass>/(offset)/<Offset>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(product_class)` setzt `ProductClass`, `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`shop/administrate`](../../richtlinien/shop.md#administrate)

Eine Richtlinie auf `shop/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezshopnavigationpart` |
| Einzelne POST-Aktionen | ShowProductsButton: ShowProducts, SortButton: Sort |
| Parameter der POST-Aktionen | ShowProducts (ProductClass: ProductClass), Sort (ProductClass: ProductClass, SortingField: SortingField, SortingOrder: SortingOrder) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/shop/productsoverview'|ezurl}>shop/productsoverview</a>
<a href={concat( '/shop/productsoverview/(product_class)/', 1 )|ezurl}>shop/productsoverview</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/shop/productsoverview' );
```

## Siehe auch

- [Alle Ansichten von shop](README.md)
- [Die Richtlinien von shop](../../richtlinien/shop.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/module.php`, `kernel/shop/productsoverview.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
