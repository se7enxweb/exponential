---
title: /class/classlist
audience: developer, admin
category: examples
summary: "Die Ansicht class/classlist: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, class, classlist, class/classlist, module, view, URL
type: generated
generated: exp:help:examples
---
# /class/classlist

Eine Ansicht des Moduls `class`, definiert in `kernel/class/module.php` und ausgeführt von `kernel/class/classlist.php`.

## Adresse

```text
/class/classlist/<GroupID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `GroupID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `class/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/class/classlist/1'|ezurl}>class/classlist</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/class/classlist/1' );
```

## Siehe auch

- [Alle Ansichten von class](README.md)
- [Die Richtlinien von class](../../richtlinien/class.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/class/module.php`, `kernel/class/classlist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
