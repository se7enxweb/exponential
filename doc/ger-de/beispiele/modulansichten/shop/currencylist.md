---
title: /shop/currencylist
audience: developer, admin
category: examples
summary: "Die Ansicht shop/currencylist: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, shop, currencylist, shop/currencylist, module, view, URL
type: generated
generated: exp:help:examples
---
# /shop/currencylist

Eine Ansicht des Moduls `shop`, definiert in `kernel/shop/module.php` und ausgeführt von `kernel/shop/currencylist.php`.

## Adresse

```text
/shop/currencylist/(offset)/<Offset>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`shop/setup`](../../richtlinien/shop.md#setup)

Eine Richtlinie auf `shop/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezshopnavigationpart` |
| Einzelne POST-Aktionen | NewCurrencyButton: NewCurrency, RemoveCurrencyButton: RemoveCurrency, ApplyChangesButton: ApplyChanges, UpdateAutopricesButton: UpdateAutoprices, UpdateAutoRatesButton: UpdateAutoRates |
| Parameter der POST-Aktionen | RemoveCurrency (DeleteCurrencyList: DeleteCurrencyList), ApplyChanges (CurrencyList: CurrencyList, Offset: Offset), UpdateAutoprices (Offset: Offset), UpdateAutoRates (Offset: Offset) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/shop/currencylist'|ezurl}>shop/currencylist</a>
<a href={concat( '/shop/currencylist/(offset)/', 1 )|ezurl}>shop/currencylist</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/shop/currencylist' );
```

## Siehe auch

- [Alle Ansichten von shop](README.md)
- [Die Richtlinien von shop](../../richtlinien/shop.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/module.php`, `kernel/shop/currencylist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
