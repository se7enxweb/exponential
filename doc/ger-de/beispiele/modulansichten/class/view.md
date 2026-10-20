---
title: /class/view
audience: developer, admin
category: examples
summary: "Die Ansicht class/view: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Ansicht, Modul, class, view, class/view, module, URL
type: generated
generated: exp:help:examples
---
# /class/view

Eine Ansicht des Moduls `class`, definiert in `kernel/class/module.php` und ausgeführt von `kernel/class/view.php`.

## Adresse

```text
/class/view/<ClassID>/(language)/<Language>/(scriptid)/<ScheduledScriptID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ClassID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(language)` setzt `Language`, `(scriptid)` setzt `ScheduledScriptID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `class/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/class/view/1'|ezurl}>class/view</a>
<a href={concat( '/class/view/1/(language)/', 1 )|ezurl}>class/view</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/class/view/1' );
```

## Siehe auch

- [Alle Ansichten von class](README.md)
- [Die Richtlinien von class](../../richtlinien/class.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/class/module.php`, `kernel/class/view.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
