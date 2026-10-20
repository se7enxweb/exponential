---
title: /state/assign
audience: developer, admin
category: examples
summary: "Die Ansicht state/assign: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, state, assign, state/assign, module, view, URL
type: generated
generated: exp:help:examples
---
# /state/assign

Eine Ansicht des Moduls `state`, definiert in `kernel/state/module.php` und ausgeführt von `kernel/state/assign.php`.

## Adresse

```text
/state/assign/<ObjectID>/<SelectedStateID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ObjectID`, `SelectedStateID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`state/assign`](../../richtlinien/state.md#assign)

Eine Richtlinie auf `state/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | AssignButton: Assign |
| Parameter der POST-Aktionen | Assign (ObjectID: ObjectID, SelectedStateIDList: SelectedStateIDList, RedirectRelativeURI: RedirectRelativeURI) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/state/assign/1/1'|ezurl}>state/assign</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/state/assign/1/1' );
```

## Siehe auch

- [Alle Ansichten von state](README.md)
- [Die Richtlinien von state](../../richtlinien/state.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/state/module.php`, `kernel/state/assign.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
