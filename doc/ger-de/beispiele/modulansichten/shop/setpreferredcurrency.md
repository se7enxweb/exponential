---
title: /shop/setpreferredcurrency
audience: developer, admin
category: examples
summary: "Die Ansicht shop/setpreferredcurrency: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, shop, setpreferredcurrency, shop/setpreferredcurrency, module, view, URL
type: generated
generated: exp:help:examples
---
# /shop/setpreferredcurrency

Eine Ansicht des Moduls `shop`, definiert in `kernel/shop/module.php` und ausgeführt von `kernel/shop/setpreferredcurrency.php`.

## Adresse

```text
/shop/setpreferredcurrency/(currency)/<Currency>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(currency)` setzt `Currency`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`shop/buy`](../../richtlinien/shop.md#buy)

Eine Richtlinie auf `shop/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezshopnavigationpart` |
| Einzelne POST-Aktionen | SetButton: Set |
| Parameter der POST-Aktionen | Set (Currency: Currency) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/shop/setpreferredcurrency'|ezurl}>shop/setpreferredcurrency</a>
<a href={concat( '/shop/setpreferredcurrency/(currency)/', 1 )|ezurl}>shop/setpreferredcurrency</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/shop/setpreferredcurrency' );
```

## Siehe auch

- [Alle Ansichten von shop](README.md)
- [Die Richtlinien von shop](../../richtlinien/shop.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/module.php`, `kernel/shop/setpreferredcurrency.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
