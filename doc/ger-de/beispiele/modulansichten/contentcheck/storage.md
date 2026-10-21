---
title: /contentcheck/storage
audience: developer, admin
category: examples
summary: "Die Ansicht contentcheck/storage: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, contentcheck, storage, contentcheck/storage, module, view, URL
type: generated
generated: exp:help:examples
---
# /contentcheck/storage

Eine Ansicht des Moduls `contentcheck`, definiert in `kernel/contentcheck/module.php` und ausgeführt von `kernel/contentcheck/storage.php`.

## Adresse

```text
/contentcheck/storage/<NodeID>/(offset)/<Offset>/(show)/<Show>/(sort)/<Sort>/(order)/<Order>/(kind)/<Kind>/(selectall)/<SelectAll>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `NodeID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`, `(show)` setzt `Show`, `(sort)` setzt `Sort`, `(order)` setzt `Order`, `(kind)` setzt `Kind`, `(selectall)` setzt `SelectAll`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`contentcheck/storage`](../../richtlinien/contentcheck.md#storage)

Eine Richtlinie auf `contentcheck/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/contentcheck/storage/1'|ezurl}>contentcheck/storage</a>
<a href={concat( '/contentcheck/storage/1/(offset)/', 1 )|ezurl}>contentcheck/storage</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/contentcheck/storage/1' );
```

## Siehe auch

- [Alle Ansichten von contentcheck](README.md)
- [Die Richtlinien von contentcheck](../../richtlinien/contentcheck.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/contentcheck/module.php`, `kernel/contentcheck/storage.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
