---
title: /section/view
audience: developer, admin
category: examples
summary: "Die Ansicht section/view: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Ansicht, Modul, section, view, section/view, module, URL
type: generated
generated: exp:help:examples
---
# /section/view

Eine Ansicht des Moduls `section`, definiert in `kernel/section/module.php` und ausgeführt von `kernel/section/view.php`.

## Adresse

```text
/section/view/<SectionID>/(offset)/<Offset>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `SectionID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`section/view`](../../richtlinien/section.md#view) oder [`section/assign`](../../richtlinien/section.md#assign)

Eine Richtlinie auf `section/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `view` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/section/view/1'|ezurl}>section/view</a>
<a href={concat( '/section/view/1/(offset)/', 1 )|ezurl}>section/view</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/section/view/1' );
```

## Siehe auch

- [Alle Ansichten von section](README.md)
- [Die Richtlinien von section](../../richtlinien/section.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/section/module.php`, `kernel/section/view.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
