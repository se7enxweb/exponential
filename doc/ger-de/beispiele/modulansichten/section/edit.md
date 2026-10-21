---
title: /section/edit
audience: developer, admin
category: examples
summary: "Die Ansicht section/edit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, section, edit, section/edit, module, view, URL
type: generated
generated: exp:help:examples
---
# /section/edit

Eine Ansicht des Moduls `section`, definiert in `kernel/section/module.php` und ausgeführt von `kernel/section/edit.php`.

## Adresse

```text
/section/edit/<SectionID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `SectionID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`section/edit`](../../richtlinien/section.md#edit)

Eine Richtlinie auf `section/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `edit` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/section/edit/1'|ezurl}>section/edit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/section/edit/1' );
```

## Siehe auch

- [Alle Ansichten von section](README.md)
- [Die Richtlinien von section](../../richtlinien/section.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/section/module.php`, `kernel/section/edit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
