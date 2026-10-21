---
title: /contentcheck/unreferenced
audience: developer, admin
category: examples
summary: "Die Ansicht contentcheck/unreferenced: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, contentcheck, unreferenced, contentcheck/unreferenced, module, view, URL
type: generated
generated: exp:help:examples
---
# /contentcheck/unreferenced

Eine Ansicht des Moduls `contentcheck`, definiert in `kernel/contentcheck/module.php` und ausgeführt von `kernel/contentcheck/unreferenced.php`.

## Adresse

```text
/contentcheck/unreferenced/<NodeID>/(offset)/<Offset>/(class)/<Class>/(sort)/<Sort>/(order)/<Order>/(containers)/<Containers>/(selectall)/<SelectAll>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `NodeID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`, `(class)` setzt `Class`, `(sort)` setzt `Sort`, `(order)` setzt `Order`, `(containers)` setzt `Containers`, `(selectall)` setzt `SelectAll`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`contentcheck/unreferenced`](../../richtlinien/contentcheck.md#unreferenced)

Eine Richtlinie auf `contentcheck/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/contentcheck/unreferenced/1'|ezurl}>contentcheck/unreferenced</a>
<a href={concat( '/contentcheck/unreferenced/1/(offset)/', 1 )|ezurl}>contentcheck/unreferenced</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/contentcheck/unreferenced/1' );
```

## Siehe auch

- [Alle Ansichten von contentcheck](README.md)
- [Die Richtlinien von contentcheck](../../richtlinien/contentcheck.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/contentcheck/module.php`, `kernel/contentcheck/unreferenced.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
