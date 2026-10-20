---
title: /shop/basket
audience: developer, admin
category: examples
summary: "Die Ansicht shop/basket: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, shop, basket, shop/basket, module, view, URL
type: generated
generated: exp:help:examples
---
# /shop/basket

Eine Ansicht des Moduls `shop`, definiert in `kernel/shop/module.php` und ausgeführt von `kernel/shop/basket.php`.

## Adresse

```text
/shop/basket/(error)/<Error>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(error)` setzt `Error`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`shop/buy`](../../richtlinien/shop.md#buy)

Eine Richtlinie auf `shop/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/shop/basket'|ezurl}>shop/basket</a>
<a href={concat( '/shop/basket/(error)/', 1 )|ezurl}>shop/basket</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/shop/basket' );
```

## Siehe auch

- [Alle Ansichten von shop](README.md)
- [Die Richtlinien von shop](../../richtlinien/shop.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/module.php`, `kernel/shop/basket.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
