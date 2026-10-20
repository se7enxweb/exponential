---
title: /shop/editvatrule
audience: developer, admin
category: examples
summary: "Die Ansicht shop/editvatrule: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, shop, editvatrule, shop/editvatrule, module, view, URL
type: generated
generated: exp:help:examples
---
# /shop/editvatrule

Eine Ansicht des Moduls `shop`, definiert in `kernel/shop/module.php` und ausgeführt von `kernel/shop/editvatrule.php`.

## Adresse

```text
/shop/editvatrule/<ruleID>/(currency)/<Currency>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ruleID`.

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
| Parameter der POST-Aktionen | Create (Country: Country, Categories: Categories, VatType: VatType), StoreChanges (RuleID: RuleID, Country: Country, Categories: Categories, VatType: VatType) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/shop/editvatrule/1'|ezurl}>shop/editvatrule</a>
<a href={concat( '/shop/editvatrule/1/(currency)/', 1 )|ezurl}>shop/editvatrule</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/shop/editvatrule/1' );
```

## Siehe auch

- [Alle Ansichten von shop](README.md)
- [Die Richtlinien von shop](../../richtlinien/shop.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/module.php`, `kernel/shop/editvatrule.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
