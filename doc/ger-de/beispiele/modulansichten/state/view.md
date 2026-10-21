---
title: /state/view
audience: developer, admin
category: examples
summary: "Die Ansicht state/view: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Ansicht, Modul, state, view, state/view, module, URL
type: generated
generated: exp:help:examples
---
# /state/view

Eine Ansicht des Moduls `state`, definiert in `kernel/state/module.php` und ausgeführt von `kernel/state/view.php`.

## Adresse

```text
/state/view/<GroupIdentifier>/<StateIdentifier>/<Language>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `GroupIdentifier`, `StateIdentifier`, `Language`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`state/administrate`](../../richtlinien/state.md#administrate)

Eine Richtlinie auf `state/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | EditButton: Edit |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/state/view/groupidentifier/stateidentifier/language'|ezurl}>state/view</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/state/view/groupidentifier/stateidentifier/language' );
```

## Siehe auch

- [Alle Ansichten von state](README.md)
- [Die Richtlinien von state](../../richtlinien/state.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/state/module.php`, `kernel/state/view.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
