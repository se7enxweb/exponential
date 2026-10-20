---
title: /shop/editcurrency
audience: developer, admin
category: examples
summary: "Die Ansicht shop/editcurrency: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, shop, editcurrency, shop/editcurrency, module, view, URL
type: generated
generated: exp:help:examples
---
# /shop/editcurrency

Eine Ansicht des Moduls `shop`, definiert in `kernel/shop/module.php` und ausgeführt von `kernel/shop/editcurrency.php`.

## Adresse

```text
/shop/editcurrency/(currency)/<Currency>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(currency)` setzt `Currency`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`shop/setup`](../../richtlinien/shop.md#setup)

Eine Richtlinie auf `shop/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezshopnavigationpart` |
| Einzelne POST-Aktionen | CancelButton: Cancel, CreateButton: Create, StoreChangesButton: StoreChanges |
| Parameter der POST-Aktionen | Create (CurrencyData: CurrencyData), StoreChanges (CurrencyData: CurrencyData, OriginalCurrencyCode: OriginalCurrencyCode) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/shop/editcurrency'|ezurl}>shop/editcurrency</a>
<a href={concat( '/shop/editcurrency/(currency)/', 1 )|ezurl}>shop/editcurrency</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/shop/editcurrency' );
```

## Siehe auch

- [Alle Ansichten von shop](README.md)
- [Die Richtlinien von shop](../../richtlinien/shop.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/module.php`, `kernel/shop/editcurrency.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
