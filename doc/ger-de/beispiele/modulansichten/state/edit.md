---
title: /state/edit
audience: developer, admin
category: examples
summary: "Die Ansicht state/edit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, state, edit, state/edit, module, view, URL
type: generated
generated: exp:help:examples
---
# /state/edit

Eine Ansicht des Moduls `state`, definiert in `kernel/state/module.php` und ausgeführt von `kernel/state/edit.php`.

## Adresse

```text
/state/edit/<GroupIdentifier>/<StateIdentifier>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `GroupIdentifier`, `StateIdentifier`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`state/administrate`](../../richtlinien/state.md#administrate)

Eine Richtlinie auf `state/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | StoreButton: Store, CancelButton: Cancel |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/state/edit/groupidentifier/stateidentifier'|ezurl}>state/edit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/state/edit/groupidentifier/stateidentifier' );
```

## Siehe auch

- [Alle Ansichten von state](README.md)
- [Die Richtlinien von state](../../richtlinien/state.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/state/module.php`, `kernel/state/edit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
