---
title: /infocollector/collectionlist
audience: developer, admin
category: examples
summary: "Die Ansicht infocollector/collectionlist: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, infocollector, collectionlist, infocollector/collectionlist, module, view, URL
type: generated
generated: exp:help:examples
---
# /infocollector/collectionlist

Eine Ansicht des Moduls `infocollector`, definiert in `kernel/infocollector/module.php` und ausgeführt von `kernel/infocollector/collectionlist.php`.

## Adresse

```text
/infocollector/collectionlist/<ObjectID>/(offset)/<Offset>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ObjectID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`infocollector/read`](../../richtlinien/infocollector.md#read)

Eine Richtlinie auf `infocollector/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `view` |
| Einzelne POST-Aktionen | RemoveCollectionsButton: RemoveCollections, ConfirmRemoveButton: ConfirmRemoval, CancelRemoveButton: CancelRemoval |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/infocollector/collectionlist/1'|ezurl}>infocollector/collectionlist</a>
<a href={concat( '/infocollector/collectionlist/1/(offset)/', 1 )|ezurl}>infocollector/collectionlist</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/infocollector/collectionlist/1' );
```

## Siehe auch

- [Alle Ansichten von infocollector](README.md)
- [Die Richtlinien von infocollector](../../richtlinien/infocollector.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/infocollector/module.php`, `kernel/infocollector/collectionlist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
