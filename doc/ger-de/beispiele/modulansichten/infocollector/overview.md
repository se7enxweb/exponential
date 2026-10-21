---
title: /infocollector/overview
audience: developer, admin
category: examples
summary: "Die Ansicht infocollector/overview: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, infocollector, overview, infocollector/overview, module, view, URL
type: generated
generated: exp:help:examples
---
# /infocollector/overview

Eine Ansicht des Moduls `infocollector`, definiert in `kernel/infocollector/module.php` und ausgeführt von `kernel/infocollector/overview.php`.

## Adresse

```text
/infocollector/overview/(offset)/<Offset>
```

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
| Einzelne POST-Aktionen | RemoveObjectCollectionButton: RemoveObjectCollection, ConfirmRemoveButton: ConfirmRemoval, CancelRemoveButton: CancelRemoval |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/infocollector/overview'|ezurl}>infocollector/overview</a>
<a href={concat( '/infocollector/overview/(offset)/', 1 )|ezurl}>infocollector/overview</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/infocollector/overview' );
```

## Siehe auch

- [Alle Ansichten von infocollector](README.md)
- [Die Richtlinien von infocollector](../../richtlinien/infocollector.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/infocollector/module.php`, `kernel/infocollector/overview.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
