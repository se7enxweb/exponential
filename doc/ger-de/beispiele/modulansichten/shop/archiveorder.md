---
title: /shop/archiveorder
audience: developer, admin
category: examples
summary: "Die Ansicht shop/archiveorder: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, shop, archiveorder, shop/archiveorder, module, view, URL
type: generated
generated: exp:help:examples
---
# /shop/archiveorder

Eine Ansicht des Moduls `shop`, definiert in `kernel/shop/module.php` und ausgeführt von `kernel/shop/archiveorder.php`.

## Adresse

```text
/shop/archiveorder
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`shop/administrate`](../../richtlinien/shop.md#administrate)

Eine Richtlinie auf `shop/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezshopnavigationpart` |
| UI-Kontext | `edit` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/shop/archiveorder'|ezurl}>shop/archiveorder</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/shop/archiveorder' );
```

## Siehe auch

- [Alle Ansichten von shop](README.md)
- [Die Richtlinien von shop](../../richtlinien/shop.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/shop/module.php`, `kernel/shop/archiveorder.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
