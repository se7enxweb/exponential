---
title: /state/groups
audience: developer, admin
category: examples
summary: "Die Ansicht state/groups: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, state, groups, state/groups, module, view, URL
type: generated
generated: exp:help:examples
---
# /state/groups

Eine Ansicht des Moduls `state`, definiert in `kernel/state/module.php` und ausgeführt von `kernel/state/groups.php`.

## Adresse

```text
/state/groups/(offset)/<Offset>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`state/administrate`](../../richtlinien/state.md#administrate)

Eine Richtlinie auf `state/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | CreateButton: Create, RemoveButton: Remove |
| Parameter der POST-Aktionen | Remove (RemoveIDList: RemoveIDList) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/state/groups'|ezurl}>state/groups</a>
<a href={concat( '/state/groups/(offset)/', 1 )|ezurl}>state/groups</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/state/groups' );
```

## Siehe auch

- [Alle Ansichten von state](README.md)
- [Die Richtlinien von state](../../richtlinien/state.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/state/module.php`, `kernel/state/groups.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
